<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class LmsClassroomMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_group_id',
        'subject_id',
        'title',
        'description',
        'meet_link',
        'google_calendar_html_link',
        'scheduled_start',
        'scheduled_end',
        'late_entry_minutes',
        'google_event_id',
        'created_by_user_id',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'late_entry_minutes' => 'integer',
    ];

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Teacher-set cap: students may join from scheduled_start until this moment (inclusive), but never after scheduled_end.
     * When late_entry_minutes is null or 0, this equals scheduled_end.
     */
    public function studentJoinDeadline(): ?Carbon
    {
        $start = $this->scheduled_start;
        $end = $this->scheduled_end;
        if ($start === null || $end === null) {
            return null;
        }
        $late = (int) ($this->late_entry_minutes ?? 0);
        if ($late <= 0) {
            return $end;
        }
        $cutoff = $start->copy()->addMinutes($late);

        return $cutoff->lessThanOrEqualTo($end) ? $cutoff : $end;
    }

    public function restrictsLateEntry(): bool
    {
        return ((int) ($this->late_entry_minutes ?? 0)) > 0;
    }

    /**
     * True once current time is strictly after scheduled_end. When no end is stored, not treated as ended (legacy meetings).
     */
    public function hasScheduledMeetEnded(?CarbonInterface $now = null): bool
    {
        $now = $now ?? now();
        $end = $this->scheduled_end;

        return $end !== null && $now->greaterThan($end);
    }

    /**
     * Direct Meet URL for teachers (same as gmeet’s join redirect). Calendar htmlLink is optional UI only.
     */
    public function teacherStartUrl(): string
    {
        $meet = trim((string) ($this->meet_link ?? ''));
        if ($meet !== '') {
            return $meet;
        }

        return trim((string) ($this->google_calendar_html_link ?? ''));
    }

    /**
     * Students may join from scheduled_start until studentJoinDeadline() (teachers are not gated here).
     */
    public function isOpenForStudentJoin(?CarbonInterface $now = null): bool
    {
        $now = $now ?? now();
        $link = trim((string) ($this->meet_link ?? ''));
        if ($link === '') {
            return false;
        }
        $start = $this->scheduled_start;
        $end = $this->scheduled_end;
        if ($start === null || $end === null) {
            return false;
        }
        if ($end->lessThanOrEqualTo($start)) {
            return false;
        }
        $deadline = $this->studentJoinDeadline();
        if ($deadline === null) {
            return false;
        }

        return $now->greaterThanOrEqualTo($start) && $now->lessThanOrEqualTo($deadline);
    }
}

