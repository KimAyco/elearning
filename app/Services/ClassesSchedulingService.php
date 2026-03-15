<?php

namespace App\Services;

use App\Models\ClassBreakBlock;
use App\Models\ClassDayProfile;
use App\Models\ClassGenerationRun;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ClassesSchedulingService
{
    private const PREFERRED_START_MIN = (8 * 60) + 30; // 08:30

    private const PREFERRED_END_MIN = (17 * 60) + 30; // 17:30

    /**
     * @var array<int, array<int, array<int, array{start:int,end:int}>>>
     */
    private array $sharedTeacherBusy = [];

    /**
     * @var array<int, int>
     */
    private array $sharedTeacherLoads = [];

    /**
     * @return array<string, mixed>
     */
    public function generateForClassGroup(int $schoolId, int $classGroupId, int $actorUserId): array
    {
        $classGroup = ClassGroup::query()
            ->with(['profile.breaks', 'semester'])
            ->where('school_id', $schoolId)
            ->find($classGroupId);

        if ($classGroup === null) {
            throw new RuntimeException('Class group does not belong to your school.');
        }

        $profile = $classGroup->profile;
        if ($profile === null) {
            throw new RuntimeException('Class group has no valid day profile.');
        }

        $slotMinutes = max(30, (int) $profile->slot_minutes);
        $days = $this->normalizeDaysMask((array) ($profile->days_mask ?? [1, 2, 3, 4, 5]));
        if ($days === []) {
            throw new RuntimeException('Class profile has no active class days.');
        }

        $breaks = $profile->breaks->sortBy('start_time')->values();
        $this->validateBreaks($profile, $breaks, $slotMinutes);

        $requiredSessions = $this->buildSessionRequirements(
            schoolId: $schoolId,
            programId: (int) $classGroup->program_id,
            yearLevel: (int) $classGroup->year_level,
            semesterId: (int) $classGroup->semester_id,
        );

        $run = ClassGenerationRun::query()->create([
            'school_id' => $schoolId,
            'class_group_id' => (int) $classGroup->id,
            'initiated_by_user_id' => $actorUserId,
            'summary_json' => [],
        ]);

        $report = DB::transaction(function () use (
            $schoolId,
            $classGroup,
            $profile,
            $slotMinutes,
            $days,
            $breaks,
            $requiredSessions,
            $run
        ): array {
            // Replace mode: clear prior draft sessions for this class group.
            ClassSession::query()
                ->where('school_id', $schoolId)
                ->where('class_group_id', (int) $classGroup->id)
                ->where('status', 'draft')
                ->delete();

            $groupBusy = $this->loadGroupBusyIntervals($schoolId, (int) $classGroup->id);
            $teacherBusy = $this->loadTeacherBusyIntervals($schoolId, (int) $classGroup->semester_id);
            $teacherLoads = $this->loadTeacherLoads($schoolId, (int) $classGroup->semester_id);

            $summary = $this->generateInternal(
                schoolId: $schoolId,
                classGroup: $classGroup,
                profile: $profile,
                slotMinutes: $slotMinutes,
                days: $days,
                breaks: $breaks,
                requiredSessions: $requiredSessions,
                runId: (int) $run->id,
                groupBusy: $groupBusy,
                teacherBusy: $teacherBusy,
                teacherLoads: $teacherLoads,
            );

            $run->summary_json = $summary;
            $run->save();

            return $summary;
        });

        return [
            'class_group_id' => $classGroupId,
            'generation_run_id' => (int) $run->id,
            'summary' => $report,
        ];
    }

    /**
     * Generate all class groups in one semester in a single conflict-aware pass.
     *
     * @return array<string, mixed>
     */
    public function generateForSemester(int $schoolId, int $semesterId, int $actorUserId): array
    {
        $classGroups = ClassGroup::query()
            ->with(['profile.breaks'])
            ->where('school_id', $schoolId)
            ->where('semester_id', $semesterId)
            ->orderBy('year_level')
            ->orderBy('name')
            ->get();

        if ($classGroups->isEmpty()) {
            throw new RuntimeException('No class groups found for selected semester.');
        }

        $this->sharedTeacherBusy = $this->loadTeacherBusyIntervals($schoolId, $semesterId);
        $this->sharedTeacherLoads = $this->loadTeacherLoads($schoolId, $semesterId);

        $groupSummaries = [];

        DB::transaction(function () use ($schoolId, $actorUserId, $classGroups, &$groupSummaries): void {
            foreach ($classGroups as $classGroup) {
                $profile = $classGroup->profile;
                if ($profile === null) {
                    $groupSummaries[] = [
                        'class_group_id' => (int) $classGroup->id,
                        'class_group_name' => (string) $classGroup->name,
                        'placed' => 0,
                        'unplaced_count' => 0,
                        'unassigned_teacher_count' => 0,
                        'unplaced' => [],
                        'unassigned_teacher' => [],
                        'info' => [['subject_code' => '-', 'message' => 'Skipped: class group has no valid day profile.']],
                    ];
                    continue;
                }

                $slotMinutes = max(30, (int) $profile->slot_minutes);
                $days = $this->normalizeDaysMask((array) ($profile->days_mask ?? [1, 2, 3, 4, 5]));
                if ($days === []) {
                    $groupSummaries[] = [
                        'class_group_id' => (int) $classGroup->id,
                        'class_group_name' => (string) $classGroup->name,
                        'placed' => 0,
                        'unplaced_count' => 0,
                        'unassigned_teacher_count' => 0,
                        'unplaced' => [],
                        'unassigned_teacher' => [],
                        'info' => [['subject_code' => '-', 'message' => 'Skipped: class profile has no active class days.']],
                    ];
                    continue;
                }

                $breaks = $profile->breaks->sortBy('start_time')->values();
                $this->validateBreaks($profile, $breaks, $slotMinutes);

                ClassSession::query()
                    ->where('school_id', $schoolId)
                    ->where('class_group_id', (int) $classGroup->id)
                    ->where('status', 'draft')
                    ->delete();

                $groupBusy = $this->loadGroupBusyIntervals($schoolId, (int) $classGroup->id);
                $requiredSessions = $this->buildSessionRequirementsSafe(
                    schoolId: $schoolId,
                    programId: (int) $classGroup->program_id,
                    yearLevel: (int) $classGroup->year_level,
                    semesterId: (int) $classGroup->semester_id,
                );

                $run = ClassGenerationRun::query()->create([
                    'school_id' => $schoolId,
                    'class_group_id' => (int) $classGroup->id,
                    'initiated_by_user_id' => $actorUserId,
                    'summary_json' => [],
                ]);

                $summary = $this->generateInternal(
                    schoolId: $schoolId,
                    classGroup: $classGroup,
                    profile: $profile,
                    slotMinutes: $slotMinutes,
                    days: $days,
                    breaks: $breaks,
                    requiredSessions: $requiredSessions,
                    runId: (int) $run->id,
                    groupBusy: $groupBusy,
                    teacherBusy: $this->sharedTeacherBusy,
                    teacherLoads: $this->sharedTeacherLoads,
                );
                $summary['class_group_id'] = (int) $classGroup->id;
                $summary['class_group_name'] = (string) $classGroup->name;

                $run->summary_json = $summary;
                $run->save();

                $groupSummaries[] = $summary;
            }
        });

        $totals = [
            'placed' => (int) collect($groupSummaries)->sum('placed'),
            'unplaced_count' => (int) collect($groupSummaries)->sum('unplaced_count'),
            'unassigned_teacher_count' => (int) collect($groupSummaries)->sum('unassigned_teacher_count'),
        ];

        return [
            'scope' => 'semester',
            'semester_id' => $semesterId,
            'groups' => $groupSummaries,
            'totals' => $totals,
        ];
    }

    /**
     * @param Collection<int, ClassBreakBlock> $breaks
     */
    public function validateBreaks(ClassDayProfile $profile, Collection $breaks, int $slotMinutes): void
    {
        $profileStart = $this->toMinutes((string) $profile->class_start_time);
        $profileEnd = $this->toMinutes((string) $profile->class_end_time);
        if ($profileEnd <= $profileStart) {
            throw new RuntimeException('Class end time must be after start time.');
        }

        $prevEnd = null;
        foreach ($breaks as $break) {
            $start = $this->toMinutes((string) $break->start_time);
            $end = $this->toMinutes((string) $break->end_time);
            if ($end <= $start) {
                throw new RuntimeException('Break end time must be after start time.');
            }
            if ($start < $profileStart || $end > $profileEnd) {
                throw new RuntimeException('Break must be within class day start/end.');
            }
            if (! $this->isSlotAligned($start, $slotMinutes) || ! $this->isSlotAligned($end, $slotMinutes)) {
                throw new RuntimeException('Break times must align to the slot size.');
            }
            if ($prevEnd !== null && $start < $prevEnd) {
                throw new RuntimeException('Break blocks cannot overlap.');
            }
            $prevEnd = $end;
        }
    }

    /**
     * @return array<int>
     */
    private function normalizeDaysMask(array $daysMask): array
    {
        $days = collect($daysMask)
            ->map(fn ($day) => (int) $day)
            ->filter(fn ($day) => $day >= 1 && $day <= 7)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $days;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSessionRequirements(int $schoolId, int $programId, int $yearLevel, int $semesterId): array
    {
        $items = DB::table('program_curriculum_items')
            ->join('subjects', 'subjects.id', '=', 'program_curriculum_items.subject_id')
            ->where('program_curriculum_items.school_id', $schoolId)
            ->where('program_curriculum_items.program_id', $programId)
            ->where('program_curriculum_items.year_level', $yearLevel)
            ->where('program_curriculum_items.semester_id', $semesterId)
            ->select(
                'subjects.id as subject_id',
                'subjects.code as subject_code',
                'subjects.weekly_hours'
            )
            ->orderBy('subjects.code')
            ->get();

        if ($items->isEmpty()) {
            throw new RuntimeException('No curriculum subjects found for selected program/year/semester.');
        }

        $sessions = [];
        $info = [];
        foreach ($items as $item) {
            $weeklyHours = (float) $item->weekly_hours;
            $subjectId = (int) $item->subject_id;
            $subjectCode = (string) $item->subject_code;

            if ($weeklyHours <= 0) {
                $info[] = ['subject_code' => $subjectCode, 'message' => 'Skipped: weekly hours is zero.'];
                continue;
            }

            if ($weeklyHours < 2) {
                $sessions[] = [
                    'subject_id' => $subjectId,
                    'subject_code' => $subjectCode,
                    'session_type' => 'lecture',
                    'duration_minutes' => 60,
                ];
                $info[] = ['subject_code' => $subjectCode, 'message' => 'Lab omitted (weekly_hours < 2).'];
                continue;
            }

            $lectureHours = (int) ceil($weeklyHours / 2);
            $labHours = (int) floor($weeklyHours / 2);

            $sessions[] = [
                'subject_id' => $subjectId,
                'subject_code' => $subjectCode,
                'session_type' => 'lecture',
                'duration_minutes' => $lectureHours * 60,
            ];
            if ($labHours > 0) {
                $sessions[] = [
                    'subject_id' => $subjectId,
                    'subject_code' => $subjectCode,
                    'session_type' => 'lab',
                    'duration_minutes' => $labHours * 60,
                ];
            }
        }

        usort($sessions, function (array $a, array $b): int {
            $durationOrder = ($b['duration_minutes'] <=> $a['duration_minutes']);
            if ($durationOrder !== 0) {
                return $durationOrder;
            }
            return strcmp((string) $a['subject_code'], (string) $b['subject_code']);
        });

        return [
            'sessions' => $sessions,
            'info' => $info,
        ];
    }

    /**
     * Safe wrapper for bulk generation: return informational warning instead of throwing.
     *
     * @return array{sessions: array<int, array<string,mixed>>, info: array<int, array<string,string>>}
     */
    private function buildSessionRequirementsSafe(int $schoolId, int $programId, int $yearLevel, int $semesterId): array
    {
        try {
            return $this->buildSessionRequirements($schoolId, $programId, $yearLevel, $semesterId);
        } catch (RuntimeException $exception) {
            return [
                'sessions' => [],
                'info' => [[
                    'subject_code' => '-',
                    'message' => $exception->getMessage(),
                ]],
            ];
        }
    }

    /**
     * @param Collection<int, ClassBreakBlock> $breaks
     * @param array<int> $days
     * @return array<int, array<int, array{start:int,end:int}>>
     */
    private function buildAvailableSegmentsByDay(ClassDayProfile $profile, Collection $breaks, array $days): array
    {
        $profileStart = $this->toMinutes((string) $profile->class_start_time);
        $profileEnd = $this->toMinutes((string) $profile->class_end_time);
        $baseSegments = [];

        $cursor = $profileStart;
        foreach ($breaks as $break) {
            $bStart = $this->toMinutes((string) $break->start_time);
            $bEnd = $this->toMinutes((string) $break->end_time);
            if ($bStart > $cursor) {
                $baseSegments[] = ['start' => $cursor, 'end' => $bStart];
            }
            $cursor = max($cursor, $bEnd);
        }
        if ($cursor < $profileEnd) {
            $baseSegments[] = ['start' => $cursor, 'end' => $profileEnd];
        }

        $segmentsByDay = [];
        foreach ($days as $day) {
            $segmentsByDay[$day] = $baseSegments;
        }

        return $segmentsByDay;
    }

    /**
     * @return array{teacher_id:int|null}
     */
    private function selectTeacherForSubject(int $schoolId, int $semesterId, int $subjectId, array $teacherLoads): array
    {
        $eligibleTeacherIds = DB::table('teacher_subjects')
            ->join('user_roles', function ($join) use ($schoolId): void {
                $join->on('user_roles.user_id', '=', 'teacher_subjects.teacher_user_id')
                    ->where('user_roles.school_id', '=', $schoolId)
                    ->where('user_roles.is_active', '=', true);
            })
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->join('users', 'users.id', '=', 'teacher_subjects.teacher_user_id')
            ->where('teacher_subjects.school_id', $schoolId)
            ->where('teacher_subjects.subject_id', $subjectId)
            ->where('roles.code', 'teacher')
            ->where('users.status', 'active')
            ->distinct()
            ->pluck('teacher_subjects.teacher_user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($eligibleTeacherIds === []) {
            return ['teacher_id' => null];
        }

        usort($eligibleTeacherIds, function (int $a, int $b) use ($teacherLoads): int {
            $loadA = (int) ($teacherLoads[$a] ?? 0);
            $loadB = (int) ($teacherLoads[$b] ?? 0);
            if ($loadA !== $loadB) {
                return $loadA <=> $loadB;
            }
            return $a <=> $b;
        });

        return ['teacher_id' => $eligibleTeacherIds[0] ?? null];
    }

    /**
     * @param array<int, array<int, array{start:int,end:int}>> $segmentsByDay
     * @param array<int, array<int, array{start:int,end:int}>> $groupBusy
     * @param array<int, array<int, array<int, array{start:int,end:int}>>> $teacherBusy
     * @return array<string, int|string>|null
     */
    private function findPlacement(
        int $durationMinutes,
        int $slotMinutes,
        array $segmentsByDay,
        array $groupBusy,
        array $teacherBusy,
        ?int $teacherId
    ): ?array {
        $orderedDays = $this->orderDaysForSpread(array_keys($segmentsByDay), $groupBusy);

        // Pass 1: exhaust preferred daytime slots first (08:30-17:30).
        $preferredSegmentsByDay = $this->filterSegmentsByWindow(
            segmentsByDay: $segmentsByDay,
            windowStart: self::PREFERRED_START_MIN,
            windowEnd: self::PREFERRED_END_MIN,
            includeInsideWindow: true,
        );
        $preferred = $this->findPlacementFromSegments(
            orderedDays: $orderedDays,
            durationMinutes: $durationMinutes,
            slotMinutes: $slotMinutes,
            segmentsByDay: $preferredSegmentsByDay,
            groupBusy: $groupBusy,
            teacherBusy: $teacherBusy,
            teacherId: $teacherId,
        );
        if ($preferred !== null) {
            return $preferred;
        }

        // Pass 2: if preferred window is saturated, allow outside preferred hours.
        $fallbackSegmentsByDay = $this->filterSegmentsByWindow(
            segmentsByDay: $segmentsByDay,
            windowStart: self::PREFERRED_START_MIN,
            windowEnd: self::PREFERRED_END_MIN,
            includeInsideWindow: false,
        );
        return $this->findPlacementFromSegments(
            orderedDays: $orderedDays,
            durationMinutes: $durationMinutes,
            slotMinutes: $slotMinutes,
            segmentsByDay: $fallbackSegmentsByDay,
            groupBusy: $groupBusy,
            teacherBusy: $teacherBusy,
            teacherId: $teacherId,
        );
    }

    /**
     * @param array<int> $days
     * @param array<int, array<int, array{start:int,end:int}>> $groupBusy
     * @return array<int>
     */
    private function orderDaysForSpread(array $days, array $groupBusy): array
    {
        usort($days, function (int $a, int $b) use ($groupBusy): int {
            $busyA = collect($groupBusy[$a] ?? [])->sum(fn (array $slot): int => ((int) $slot['end']) - ((int) $slot['start']));
            $busyB = collect($groupBusy[$b] ?? [])->sum(fn (array $slot): int => ((int) $slot['end']) - ((int) $slot['start']));
            if ($busyA !== $busyB) {
                return $busyA <=> $busyB;
            }
            return $a <=> $b;
        });

        return $days;
    }

    /**
     * @param array<int, array<int, array{start:int,end:int}>> $segmentsByDay
     * @return array<int, array<int, array{start:int,end:int}>>
     */
    private function filterSegmentsByWindow(
        array $segmentsByDay,
        int $windowStart,
        int $windowEnd,
        bool $includeInsideWindow,
    ): array {
        $filtered = [];
        foreach ($segmentsByDay as $day => $segments) {
            $daySegments = [];
            foreach ($segments as $segment) {
                $start = (int) $segment['start'];
                $end = (int) $segment['end'];
                if ($end <= $start) {
                    continue;
                }

                if ($includeInsideWindow) {
                    $insideStart = max($start, $windowStart);
                    $insideEnd = min($end, $windowEnd);
                    if ($insideEnd > $insideStart) {
                        $daySegments[] = ['start' => $insideStart, 'end' => $insideEnd];
                    }
                    continue;
                }

                // Outside window parts: before preferred start and after preferred end.
                if ($start < $windowStart) {
                    $beforeEnd = min($end, $windowStart);
                    if ($beforeEnd > $start) {
                        $daySegments[] = ['start' => $start, 'end' => $beforeEnd];
                    }
                }
                if ($end > $windowEnd) {
                    $afterStart = max($start, $windowEnd);
                    if ($end > $afterStart) {
                        $daySegments[] = ['start' => $afterStart, 'end' => $end];
                    }
                }
            }
            $filtered[(int) $day] = $daySegments;
        }

        return $filtered;
    }

    /**
     * @param array<int> $orderedDays
     * @param array<int, array<int, array{start:int,end:int}>> $segmentsByDay
     * @param array<int, array<int, array{start:int,end:int}>> $groupBusy
     * @param array<int, array<int, array<int, array{start:int,end:int}>>> $teacherBusy
     * @return array<string, int|string>|null
     */
    private function findPlacementFromSegments(
        array $orderedDays,
        int $durationMinutes,
        int $slotMinutes,
        array $segmentsByDay,
        array $groupBusy,
        array $teacherBusy,
        ?int $teacherId,
    ): ?array {
        foreach ($orderedDays as $day) {
            foreach (($segmentsByDay[$day] ?? []) as $segment) {
                $start = (int) $segment['start'];
                $end = (int) $segment['end'];
                $candidateStart = $this->alignToNextSlot($start, $slotMinutes);
                for ($candidate = $candidateStart; ($candidate + $durationMinutes) <= $end; $candidate += $slotMinutes) {
                    $candidateEnd = $candidate + $durationMinutes;
                    if ($this->hasOverlap($groupBusy[$day] ?? [], $candidate, $candidateEnd)) {
                        continue;
                    }
                    if ($teacherId !== null && $this->hasOverlap($teacherBusy[$teacherId][$day] ?? [], $candidate, $candidateEnd)) {
                        continue;
                    }

                    return [
                        'day_of_week' => (int) $day,
                        'start_min' => $candidate,
                        'end_min' => $candidateEnd,
                        'start_time' => $this->toTime($candidate),
                        'end_time' => $this->toTime($candidateEnd),
                    ];
                }
            }
        }

        return null;
    }

    /**
     * @return array<int, array<int, array{start:int,end:int}>>
     */
    private function loadGroupBusyIntervals(int $schoolId, int $classGroupId): array
    {
        $rows = ClassSession::query()
            ->where('school_id', $schoolId)
            ->where('class_group_id', $classGroupId)
            ->whereIn('status', ['draft', 'locked'])
            ->get(['day_of_week', 'start_time', 'end_time']);

        $busy = [];
        foreach ($rows as $row) {
            $day = (int) $row->day_of_week;
            if (! isset($busy[$day])) {
                $busy[$day] = [];
            }
            $busy[$day][] = [
                'start' => $this->toMinutes((string) $row->start_time),
                'end' => $this->toMinutes((string) $row->end_time),
            ];
        }
        return $busy;
    }

    /**
     * @return array<int, array<int, array<int, array{start:int,end:int}>>>
     */
    private function loadTeacherBusyIntervals(int $schoolId, int $semesterId): array
    {
        $rows = ClassSession::query()
            ->join('class_groups', 'class_groups.id', '=', 'class_sessions.class_group_id')
            ->where('class_sessions.school_id', $schoolId)
            ->where('class_groups.semester_id', $semesterId)
            ->whereNotNull('class_sessions.teacher_user_id')
            ->whereIn('class_sessions.status', ['draft', 'locked'])
            ->get([
                'class_sessions.teacher_user_id',
                'class_sessions.day_of_week',
                'class_sessions.start_time',
                'class_sessions.end_time',
            ]);

        $busy = [];
        foreach ($rows as $row) {
            $teacherId = (int) $row->teacher_user_id;
            $day = (int) $row->day_of_week;
            if (! isset($busy[$teacherId])) {
                $busy[$teacherId] = [];
            }
            if (! isset($busy[$teacherId][$day])) {
                $busy[$teacherId][$day] = [];
            }
            $busy[$teacherId][$day][] = [
                'start' => $this->toMinutes((string) $row->start_time),
                'end' => $this->toMinutes((string) $row->end_time),
            ];
        }

        return $busy;
    }

    /**
     * @return array<int, int>
     */
    private function loadTeacherLoads(int $schoolId, int $semesterId): array
    {
        $rows = ClassSession::query()
            ->join('class_groups', 'class_groups.id', '=', 'class_sessions.class_group_id')
            ->where('class_sessions.school_id', $schoolId)
            ->where('class_groups.semester_id', $semesterId)
            ->whereNotNull('class_sessions.teacher_user_id')
            ->whereIn('class_sessions.status', ['draft', 'locked'])
            ->select('class_sessions.teacher_user_id')
            ->selectRaw('COALESCE(SUM(class_sessions.duration_minutes), 0) as total_minutes')
            ->groupBy('class_sessions.teacher_user_id')
            ->get();

        $loads = [];
        foreach ($rows as $row) {
            $loads[(int) $row->teacher_user_id] = (int) ($row->total_minutes ?? 0);
        }

        return $loads;
    }

    /**
     * @param array<int, array{start:int,end:int}> $intervals
     */
    private function hasOverlap(array $intervals, int $start, int $end): bool
    {
        foreach ($intervals as $interval) {
            if ((int) $interval['start'] < $end && (int) $interval['end'] > $start) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array<int, array<int, array{start:int,end:int}>> $busyByDay
     */
    private function pushInterval(array &$busyByDay, int $day, int $start, int $end): void
    {
        if (! isset($busyByDay[$day])) {
            $busyByDay[$day] = [];
        }
        $busyByDay[$day][] = ['start' => $start, 'end' => $end];
    }

    private function toMinutes(string $time): int
    {
        [$h, $m, $s] = array_pad(explode(':', $time), 3, '0');
        return ((int) $h * 60) + (int) $m;
    }

    private function toTime(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return sprintf('%02d:%02d:00', $h, $m);
    }

    private function isSlotAligned(int $value, int $slotMinutes): bool
    {
        return ($value % $slotMinutes) === 0;
    }

    private function alignToNextSlot(int $value, int $slotMinutes): int
    {
        if ($this->isSlotAligned($value, $slotMinutes)) {
            return $value;
        }

        return $value + ($slotMinutes - ($value % $slotMinutes));
    }

    /**
     * @param array<int, array<string,mixed>> $requiredSessions
     * @param array<int, array<int, array{start:int,end:int}>> $groupBusy
     * @param array<int, array<int, array<int, array{start:int,end:int}>>> $teacherBusy
     * @param array<int, int> $teacherLoads
     * @return array<string,mixed>
     */
    private function generateInternal(
        int $schoolId,
        ClassGroup $classGroup,
        ClassDayProfile $profile,
        int $slotMinutes,
        array $days,
        Collection $breaks,
        array $requiredSessions,
        int $runId,
        array &$groupBusy,
        array &$teacherBusy,
        array &$teacherLoads,
    ): array {
        $placed = 0;
        $unplaced = [];
        $unassignedTeacher = [];
        $info = $requiredSessions['info'] ?? [];
        $segmentsByDay = $this->buildAvailableSegmentsByDay($profile, $breaks, $days);

        foreach (($requiredSessions['sessions'] ?? []) as $sessionSpec) {
            $subjectId = (int) $sessionSpec['subject_id'];
            $subjectCode = (string) $sessionSpec['subject_code'];
            $duration = (int) $sessionSpec['duration_minutes'];
            $sessionType = (string) $sessionSpec['session_type'];

            $teacherChoice = $this->selectTeacherForSubject(
                schoolId: $schoolId,
                semesterId: (int) $classGroup->semester_id,
                subjectId: $subjectId,
                teacherLoads: $teacherLoads,
            );
            $teacherId = $teacherChoice['teacher_id'];
            if ($teacherId === null) {
                $unassignedTeacher[] = [
                    'subject_code' => $subjectCode,
                    'session_type' => $sessionType,
                    'reason' => 'No eligible teaching staff yet. Session is scheduled with TBA.',
                ];
            }

            $placement = $this->findPlacement(
                durationMinutes: $duration,
                slotMinutes: $slotMinutes,
                segmentsByDay: $segmentsByDay,
                groupBusy: $groupBusy,
                teacherBusy: $teacherBusy,
                teacherId: $teacherId,
            );

            if ($placement === null) {
                $unplaced[] = [
                    'subject_code' => $subjectCode,
                    'session_type' => $sessionType,
                    'duration_minutes' => $duration,
                    'reason' => $teacherId === null
                        ? 'No available slot in class windows. Teacher remains TBA.'
                        : 'No available slot without teacher/class conflict.',
                ];
                continue;
            }

            $created = ClassSession::query()->create([
                'school_id' => $schoolId,
                'class_group_id' => (int) $classGroup->id,
                'subject_id' => $subjectId,
                'session_type' => $sessionType,
                'duration_minutes' => $duration,
                'day_of_week' => $placement['day_of_week'],
                'start_time' => $placement['start_time'],
                'end_time' => $placement['end_time'],
                'teacher_user_id' => $teacherId,
                'generation_run_id' => $runId,
                'status' => 'draft',
            ]);

            $placed++;

            $this->pushInterval($groupBusy, (int) $placement['day_of_week'], $placement['start_min'], $placement['end_min']);
            if ($teacherId !== null) {
                if (! isset($teacherBusy[$teacherId])) {
                    $teacherBusy[$teacherId] = [];
                }
                $this->pushInterval($teacherBusy[$teacherId], (int) $placement['day_of_week'], $placement['start_min'], $placement['end_min']);
                $teacherLoads[$teacherId] = ($teacherLoads[$teacherId] ?? 0) + (int) $created->duration_minutes;
            }
        }

        return [
            'placed' => $placed,
            'unplaced_count' => count($unplaced),
            'unassigned_teacher_count' => count($unassignedTeacher),
            'unplaced' => $unplaced,
            'unassigned_teacher' => $unassignedTeacher,
            'info' => $info,
        ];
    }
}
