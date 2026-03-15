<?php

use App\Http\Controllers\ApplicantEnrollmentController;
use App\Http\Controllers\PublicSchoolController;
use App\Http\Controllers\SchoolRegistrationController;
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\SchoolController as SuperAdminSchoolController;
use App\Http\Controllers\Tenant\AuthController as TenantAuthController;
use App\Http\Controllers\Tenant\PortalController as TenantPortalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSchoolController::class, 'index']);
Route::get('/register/school', [SchoolRegistrationController::class, 'showForm'])->name('register.school.form');
Route::post('/register/school', [SchoolRegistrationController::class, 'submit'])->name('register.school.submit');
Route::get('/register/school/{registration}/verify-email', [SchoolRegistrationController::class, 'verifyEmailForm'])->name('register.school.verify');
Route::post('/register/school/{registration}/verify-email', [SchoolRegistrationController::class, 'verifyEmailSubmit'])->name('register.school.verify.submit');
Route::post('/register/school/{registration}/verify-email/resend', [SchoolRegistrationController::class, 'resendEmailCode'])->name('register.school.verify.resend');
Route::get('/register/school/{registration}/payment', [SchoolRegistrationController::class, 'payment'])->name('register.school.payment');
Route::post('/register/school/{registration}/payment', [SchoolRegistrationController::class, 'confirmPayment'])->name('register.school.payment.confirm');
Route::get('/schools/{school_code}/login', [PublicSchoolController::class, 'schoolLogin'])->name('school.login');
Route::get('/schools/{school_code}/enroll', [PublicSchoolController::class, 'schoolEnroll'])->name('school.enroll');

// Applicant Enrollment Wizard Routes
Route::prefix('enroll/{school_code}')->group(function (): void {
    Route::get('/step1', [ApplicantEnrollmentController::class, 'step1'])->name('enroll.step1');
    Route::post('/step1', [ApplicantEnrollmentController::class, 'processStep1']);
    Route::get('/verify-email', [ApplicantEnrollmentController::class, 'verifyEmailForm'])->name('enroll.verify');
    Route::post('/verify-email', [ApplicantEnrollmentController::class, 'verifyEmailSubmit'])->name('enroll.verify.submit');
    Route::post('/verify-email/resend', [ApplicantEnrollmentController::class, 'resendEmailCode'])->name('enroll.verify.resend');
    Route::get('/step2', [ApplicantEnrollmentController::class, 'step2'])->name('enroll.step2');
    Route::post('/step2', [ApplicantEnrollmentController::class, 'processStep2']);
    Route::get('/step3', [ApplicantEnrollmentController::class, 'step3'])->name('enroll.step3');
    Route::post('/step3', [ApplicantEnrollmentController::class, 'processStep3']);
    Route::get('/step4', [ApplicantEnrollmentController::class, 'step4'])->name('enroll.step4');
    Route::post('/complete', [ApplicantEnrollmentController::class, 'complete'])->name('enroll.complete');
    Route::post('/pay/paymongo', [ApplicantEnrollmentController::class, 'initiatePayMongoEnrollment'])->name('enroll.paymongo.initiate');
    Route::get('/paymongo/success', [ApplicantEnrollmentController::class, 'paymongoEnrollmentSuccess'])->name('enroll.paymongo.success');
    Route::get('/stripe/success', [ApplicantEnrollmentController::class, 'stripeSuccess'])->name('enroll.stripe.success');
    Route::get('/success', [ApplicantEnrollmentController::class, 'success'])->name('enroll.success');
});

Route::get('/superadmin/login', [SuperAdminAuthController::class, 'showLoginForm']);
Route::post('/superadmin/login', [SuperAdminAuthController::class, 'login'])->middleware('throttle:superadmin-login');
Route::post('/superadmin/logout', [SuperAdminAuthController::class, 'logout'])->middleware('superadmin.auth');

Route::middleware('superadmin.auth')->prefix('superadmin')->group(function (): void {
    Route::get('/schools', [SuperAdminSchoolController::class, 'index']);
    Route::post('/schools', [SuperAdminSchoolController::class, 'store']);
    Route::post('/pricing/platform', [SuperAdminSchoolController::class, 'updatePlatformPricing']);
    Route::post('/pricing/plans', [SuperAdminSchoolController::class, 'upsertPaymentPlan']);
    Route::patch('/schools/{school}/status', [SuperAdminSchoolController::class, 'updateStatus']);
    Route::post('/registrations/{registration}/approve', [SuperAdminSchoolController::class, 'approveRegistration']);
    Route::get('/schools/{school}/subscription', [SuperAdminSchoolController::class, 'subscription']);
});

Route::get('/login', [TenantAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [TenantAuthController::class, 'login'])->middleware('throttle:tenant-login');
Route::post('/logout', [TenantAuthController::class, 'logout']);
Route::get('/tenant/session', [TenantAuthController::class, 'sessionInfo'])->middleware('tenant.context');

Route::middleware('tenant.context')->prefix('tenant')->group(function (): void {
    Route::get('/dashboard', [TenantPortalController::class, 'dashboard']);
    Route::get('/lms', [TenantPortalController::class, 'lmsPage']);
    Route::get('/lms/classes/{classGroup}/{subject}', [TenantPortalController::class, 'lmsClassDashboard'])->middleware('tenant.permission:teacher.content.manage');
    Route::post('/lms/classes/{classGroup}/{subject}/lessons', [TenantPortalController::class, 'lmsStoreLesson'])->middleware('tenant.permission:teacher.content.manage');
    Route::post('/lms/classes/{classGroup}/{subject}/modules', [TenantPortalController::class, 'lmsStoreModule'])->middleware('tenant.permission:teacher.content.manage');
    Route::get('/lms/modules/{module}/download', [TenantPortalController::class, 'lmsDownloadModule'])->middleware('tenant.permission:teacher.content.manage');

    // LMS Quizzes
    Route::get('/lms/classes/{classGroup}/{subject}/gradebook', [\App\Http\Controllers\Tenant\QuizController::class, 'gradebook']);
    Route::post('/lms/classes/{classGroup}/{subject}/quizzes', [\App\Http\Controllers\Tenant\QuizController::class, 'store']);
    Route::post('/lms/quizzes/{quiz}/questions', [\App\Http\Controllers\Tenant\QuizController::class, 'addQuestion']);
    Route::post('/lms/quizzes/{quiz}/publish', [\App\Http\Controllers\Tenant\QuizController::class, 'publish']);
    Route::get('/lms/quizzes/{quiz}/results', [\App\Http\Controllers\Tenant\QuizController::class, 'results']);
        Route::post('/lms/attempts/{attempt}/grade', [\App\Http\Controllers\Tenant\QuizController::class, 'gradeEssay']);

        // Student Quiz Routes
        Route::get('/lms/quizzes/{quiz}', [\App\Http\Controllers\Tenant\QuizController::class, 'show']);
        Route::post('/lms/quizzes/{quiz}/start', [\App\Http\Controllers\Tenant\QuizController::class, 'start']);
        Route::post('/lms/quizzes/{quiz}/submit', [\App\Http\Controllers\Tenant\QuizController::class, 'submit']);
    Route::get('/classes/{classGroup}/{subject}', [TenantPortalController::class, 'studentLessonsPage']);
    Route::get('/class', [TenantPortalController::class, 'classPage']);

    Route::get('/enrollments', [TenantPortalController::class, 'enrollmentsPage']);
    Route::post('/enrollments/select', [TenantPortalController::class, 'enrollSelect'])->middleware('tenant.permission:student.enrollment.create');
    Route::post('/enrollments/plan', [TenantPortalController::class, 'submitStudentPlan'])->middleware('tenant.permission:student.enrollment.create');
    Route::post('/enrollments/{enrollment}/confirm', [TenantPortalController::class, 'enrollConfirm'])->middleware('tenant.permission:registrar.enrollment.confirm');
    Route::post('/enrollments/students/{user}/activate', [TenantPortalController::class, 'registrarActivateStudentAccount'])->middleware('tenant.permission:registrar.enrollment.confirm');

    // Finance Module
    Route::get('/billing', [TenantPortalController::class, 'billingPage']);
    Route::post('/billing/fee-settings/enrollment/general', [TenantPortalController::class, 'saveGeneralEnrollmentFee'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/fee-settings/{feeSetting}/enrollment', [TenantPortalController::class, 'updateEnrollmentFee'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/subjects/{subject}/price', [TenantPortalController::class, 'updateCoursePrice'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/categories', [TenantPortalController::class, 'createBillingCategory'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/groups', [TenantPortalController::class, 'createBillingGroup'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/groups/{group}/update', [TenantPortalController::class, 'updateBillingGroup'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/groups/{group}/delete', [TenantPortalController::class, 'deleteBillingGroup'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/rules', [TenantPortalController::class, 'createBillingRule'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/rules/{rule}/update', [TenantPortalController::class, 'updateBillingRule'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/rules/{rule}/delete', [TenantPortalController::class, 'deleteBillingRule'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/{billing}/payments', [TenantPortalController::class, 'submitPayment'])->middleware('tenant.permission:student.records.view');
    Route::post('/payments/{payment}/verify', [TenantPortalController::class, 'verifyPayment'])->middleware('tenant.permission:finance.payment.verify');
    Route::post('/billing/{billing}/clearance', [TenantPortalController::class, 'issueClearance'])->middleware('tenant.permission:finance.clearance.issue');
    Route::post('/billing/{billing}/clearance/approve', [TenantPortalController::class, 'approveClearance'])->middleware('tenant.permission:registrar.enrollment.confirm');
    Route::post('/billing/clearance/approve-all-for-student', [TenantPortalController::class, 'approveAllClearancesForStudent'])->middleware('tenant.permission:registrar.enrollment.confirm');

    // Student payments
    Route::get('/payments', [TenantPortalController::class, 'paymentsPage'])->middleware('tenant.permission:student.records.view');
    Route::get('/billing/{billing}/review', [TenantPortalController::class, 'payBillingReview'])->middleware('tenant.permission:student.records.view');
    Route::get('/billing/{billing}/pay/stripe', [TenantPortalController::class, 'payBillingStripe'])->middleware('tenant.permission:student.records.view');
    Route::get('/payments/stripe/success', [TenantPortalController::class, 'stripePaymentSuccess'])->middleware('tenant.permission:student.records.view');
    Route::post('/billing/{billing}/pay/paymongo', [TenantPortalController::class, 'payBillingPayMongo'])->middleware('tenant.permission:student.records.view');
    Route::get('/payments/paymongo/success', [TenantPortalController::class, 'paymongoPaymentSuccess'])->middleware('tenant.permission:student.records.view');

    Route::get('/cashier', [TenantPortalController::class, 'cashierPage'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/cashier/payments', [TenantPortalController::class, 'cashierCreatePayment'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/cashier/settings/auto-approve', [TenantPortalController::class, 'cashierToggleAutoApprovePayments'])->middleware('tenant.permission:finance.billing.manage');

    Route::get('/discount', [TenantPortalController::class, 'discountPage'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/discount', [TenantPortalController::class, 'createDiscount'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/discount/{discount}/update', [TenantPortalController::class, 'updateDiscount'])->middleware('tenant.permission:finance.billing.manage');

    Route::get('/scholarship', [TenantPortalController::class, 'scholarshipPage'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/scholarship', [TenantPortalController::class, 'createScholarship'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/scholarship/{scholarship}/update', [TenantPortalController::class, 'updateScholarship'])->middleware('tenant.permission:finance.billing.manage');

    Route::get('/student-wallet', [TenantPortalController::class, 'studentWalletPage'])->middleware('tenant.permission:finance.billing.manage');
    Route::post('/student-wallet/{user}/add', [TenantPortalController::class, 'addStudentWallet'])->middleware('tenant.permission:finance.billing.manage');

    Route::get('/grades', [TenantPortalController::class, 'gradesPage']);
    Route::post('/grades/draft', [TenantPortalController::class, 'gradeDraft'])->middleware('tenant.permission:teacher.grades.submit');
    Route::post('/grades/{grade}/submit', [TenantPortalController::class, 'gradeSubmit'])->middleware('tenant.permission:teacher.grades.submit');
    Route::post('/grades/{grade}/dean-decision', [TenantPortalController::class, 'gradeDeanDecision'])->middleware('tenant.permission:dean.grades.review');
    Route::post('/grades/{grade}/finalize', [TenantPortalController::class, 'gradeFinalize'])->middleware('tenant.permission:registrar.grades.finalize');
    Route::post('/grades/{grade}/release', [TenantPortalController::class, 'gradeRelease'])->middleware('tenant.permission:registrar.grades.release');

    Route::get('/school-page', [TenantPortalController::class, 'schoolPage'])->middleware('tenant.permission:school_admin.manage_staff');
    Route::post('/school-page', [TenantPortalController::class, 'updateSchoolPage'])->middleware('tenant.permission:school_admin.manage_staff');

    Route::get('/admin', [TenantPortalController::class, 'adminPage']);
    Route::get('/admin/structure/add', [TenantPortalController::class, 'adminAddStructurePage']);
    Route::get('/admin/subjects', fn () => redirect('/tenant/admin?tab=subjects'));
    Route::get('/admin/classes', function (Request $request) {
        $query = array_merge($request->query(), ['tab' => 'classes']);
        return redirect('/tenant/admin?' . http_build_query($query));
    });
    Route::get('/admin/class-groups', [TenantPortalController::class, 'adminClassGroupsPage']);
    Route::get('/admin/classes/timetable', [TenantPortalController::class, 'adminWeeklyTimetablePage']);
    Route::get('/admin/classes/teacher-schedule', [TenantPortalController::class, 'adminTeacherSchedulePage']);
    Route::post('/admin/classes/groups/{group}/settings', [TenantPortalController::class, 'adminUpdateClassGroupSettings'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::get('/admin/roles', fn () => redirect('/tenant/admin?tab=roles'));
    Route::get('/admin/students', fn () => redirect('/tenant/admin?tab=students'));
    Route::post('/admin/classes/current-term', [TenantPortalController::class, 'adminSetCurrentTerm'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/classes/generate-semester', [TenantPortalController::class, 'adminGenerateSemesterSchedules'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/classes/schedules/clear-all', [TenantPortalController::class, 'adminClearAllGeneratedSchedules'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/users', [TenantPortalController::class, 'adminCreateUser'])->middleware('tenant.permission:school_admin.manage_staff');
    Route::post('/admin/users/{user}/status', [TenantPortalController::class, 'adminUpdateUserStatus']);
    Route::post('/admin/teachers/subjects/sync', [TenantPortalController::class, 'adminSyncTeacherSubjects'])->middleware('tenant.permission:school_admin.manage_staff');
    Route::post('/admin/roles/assign', [TenantPortalController::class, 'adminAssignRole'])->middleware('tenant.permission:school_admin.assign_roles');
    Route::post('/admin/roles/assign-bulk', [TenantPortalController::class, 'adminAssignRolesBulk'])->middleware('tenant.permission:school_admin.assign_roles');
    
    // Curriculum Management
    Route::post('/admin/academic-years', [TenantPortalController::class, 'adminCreateAcademicYear'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/colleges', [TenantPortalController::class, 'adminCreateCollege'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/departments', [TenantPortalController::class, 'adminCreateDepartment'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/programs', [TenantPortalController::class, 'adminCreateProgram'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/curriculum-items', [TenantPortalController::class, 'adminAddCurriculumItem'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/curriculum-items/{curriculumItem}/remove', [TenantPortalController::class, 'adminRemoveCurriculumItem'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/subjects', [TenantPortalController::class, 'adminCreateSubject'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/subjects/{subject}/update', [TenantPortalController::class, 'adminUpdateSubject'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/semesters', [TenantPortalController::class, 'adminCreateSemester'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/offerings', [TenantPortalController::class, 'adminCreateOffering'])->middleware('tenant.permission:school_admin.manage_curriculum');
    Route::post('/admin/sections', [TenantPortalController::class, 'adminCreateSection'])->middleware('tenant.permission:school_admin.manage_curriculum');
    
    Route::post('/dean/assign-teacher', [TenantPortalController::class, 'deanAssignTeacher'])->middleware('tenant.permission:dean.subject_assign');
});
