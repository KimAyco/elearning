<?php

use App\Http\Controllers\Tenant\AdminController;
use App\Http\Controllers\Tenant\BillingController;
use App\Http\Controllers\Tenant\EnrollmentController;
use App\Http\Controllers\Tenant\GradeController;
use App\Http\Controllers\Tenant\SubjectOfferingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'tenant.context'])->prefix('tenant')->group(function (): void {
    Route::post('/enrollments/select', [EnrollmentController::class, 'select'])
        ->middleware('tenant.permission:student.enrollment.create');
    Route::get('/enrollments/mine', [EnrollmentController::class, 'mine'])
        ->middleware('tenant.permission:student.records.view');
    Route::post('/enrollments/{enrollment}/confirm', [EnrollmentController::class, 'confirm'])
        ->middleware('tenant.permission:registrar.enrollment.confirm');

    Route::post('/billing/rules', [BillingController::class, 'createRule'])
        ->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/enrollments/{enrollment}/generate', [BillingController::class, 'generateForEnrollment'])
        ->middleware('tenant.permission:finance.billing.manage');
    Route::post('/billing/{billing}/payments', [BillingController::class, 'submitPayment'])
        ->middleware('tenant.permission:student.records.view');
    Route::get('/billing/mine', [BillingController::class, 'myBilling'])
        ->middleware('tenant.permission:student.records.view');
    Route::post('/payments/{payment}/verify', [BillingController::class, 'verifyPayment'])
        ->middleware('tenant.permission:finance.payment.verify');
    Route::post('/billing/{billing}/clearance', [BillingController::class, 'issueClearance'])
        ->middleware('tenant.permission:finance.clearance.issue');

    Route::post('/grades/draft', [GradeController::class, 'upsertDraft'])
        ->middleware('tenant.permission:teacher.grades.submit');
    Route::post('/grades/{grade}/submit', [GradeController::class, 'submit'])
        ->middleware('tenant.permission:teacher.grades.submit');
    Route::post('/grades/{grade}/return-to-draft', [GradeController::class, 'returnToDraft'])
        ->middleware('tenant.permission:teacher.grades.submit');
    Route::post('/grades/{grade}/dean-decision', [GradeController::class, 'deanDecision'])
        ->middleware('tenant.permission:dean.grades.review');
    Route::post('/grades/{grade}/finalize', [GradeController::class, 'finalize'])
        ->middleware('tenant.permission:registrar.grades.finalize');
    Route::post('/grades/{grade}/release', [GradeController::class, 'release'])
        ->middleware('tenant.permission:registrar.grades.release');
    Route::get('/grades/mine/released', [GradeController::class, 'myReleasedGrades'])
        ->middleware('tenant.permission:student.grades.view');

    Route::patch('/subject-offerings/{subjectOffering}/assign-teacher', [SubjectOfferingController::class, 'assignTeacher'])
        ->middleware('tenant.permission:dean.subject_assign');

    Route::post('/admin/roles/assign', [AdminController::class, 'assignRole'])
        ->middleware('tenant.permission:school_admin.assign_roles');
    Route::post('/admin/subjects', [AdminController::class, 'createSubject'])
        ->middleware('tenant.permission:school_admin.manage_curriculum');
});
