<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Department;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PublicSchoolController extends Controller
{
    public function index(): View
    {
        $schools = School::query()
            ->where('status', 'active')
            ->withCount(['userRoles as enrolled_students_count' => function ($query) {
                $query->where('is_active', true)
                    ->whereHas('role', function ($roleQuery): void {
                        $roleQuery->where('code', 'student');
                    });
            }])
            ->orderBy('name')
            ->get();

        return view('public.index', [
            'schools' => $schools,
        ]);
    }

    public function schoolLogin(string $school_code): View|RedirectResponse
    {
        $school = School::query()
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        return view('tenant.login', [
            'prefillSchoolCode' => $school->school_code,
            'schoolName'        => $school->name,
            'schoolLogoUrl'     => $school->schoolSealLogoUrl() ?? $school->logo_url,
            'schoolCoverUrl'    => $school->cover_image_url,
            'schoolTheme'       => $school->theme,
        ]);
    }

    public function schoolEnroll(string $school_code): View|RedirectResponse
    {
        $school = School::query()
            ->with('profile')
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        $departments = Department::query()
            ->where('school_id', (int) $school->id)
            ->where('status', 'active')
            ->with(['programs' => function ($query): void {
                $query->where('status', 'active')->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('public.school-enroll', [
            'school' => $school,
            'departments' => $departments,
        ]);
    }
}
