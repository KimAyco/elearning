<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\ClassGroup;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Program;
use App\Models\School;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\SubjectOffering;
use App\Models\User;
use App\Models\UserRole;
use App\Models\FinanceSetting;
use App\Services\AuditLogService;
use App\Services\PayMongoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class ApplicantEnrollmentController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly PayMongoService $payMongoService,
    ) {}

    /**
     * Step 1: Personal Information Form
     */
    public function step1(string $school_code): View|RedirectResponse
    {
        $school = School::query()
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        return view('public.enroll.step1', [
            'school'   => $school,
        ]);
    }

    /**
     * Step 1: Process Personal Information
     */
    public function processStep1(Request $request, string $school_code): RedirectResponse
    {
        $school = School::query()
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        $payload = $request->validate([
            'full_name'     => ['required', 'string', 'max:120'],
            'birth_date'    => ['required', 'date', 'before:today'],
            'gender'        => ['required', 'in:male,female,other'],
            'email'         => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone'         => ['nullable', 'string', 'max:30'],
            'address'       => ['nullable', 'string', 'max:500'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Store applicant data in session
        $request->session()->put('enrollment_applicant', [
            'school_id'   => $school->id,
            'school_code' => $school->school_code,
            'full_name'   => $payload['full_name'],
            'birth_date'  => $payload['birth_date'],
            'gender'      => $payload['gender'],
            'email'       => $payload['email'],
            'phone'       => $payload['phone'] ?? null,
            'address'     => $payload['address'] ?? null,
            'password_hash' => Hash::make($payload['password']),
        ]);

        $code = (string) random_int(100000, 999999);
        $request->session()->put('enrollment_email_verification', [
            'email' => $payload['email'],
            'code' => $code,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);
        $request->session()->forget('enrollment_email_verified');

        $this->sendBrevoVerification($payload['email'], $payload['full_name'], $code);

        return redirect()->route('enroll.verify', ['school_code' => $school->school_code])->with('status', 'We sent a verification code to your email.');
    }

    /**
     * Step 2: Deprecated in favor of direct enrollment-fee payment.
     */
    public function step2(string $school_code): RedirectResponse
    {
        return redirect()->route('enroll.step4', ['school_code' => $school_code]);
    }

    /**
     * Step 2: Deprecated in favor of direct enrollment-fee payment.
     */
    public function processStep2(Request $request, string $school_code): RedirectResponse
    {
        return redirect()->route('enroll.step4', ['school_code' => $school_code]);
    }

    /**
     * Step 3: Deprecated in favor of direct enrollment-fee payment.
     */
    public function step3(string $school_code): RedirectResponse
    {
        return redirect()->route('enroll.step4', ['school_code' => $school_code]);
    }

    /**
     * Step 3: Deprecated in favor of direct enrollment-fee payment.
     */
    public function processStep3(Request $request, string $school_code): RedirectResponse
    {
        return redirect()->route('enroll.step4', ['school_code' => $school_code]);
    }

    /**
     * Step 4: Enrollment Fee Payment
     */
    public function step4(string $school_code): View|RedirectResponse
    {
        $school = School::query()
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        $applicant = session('enrollment_applicant');
        if ($applicant === null || $applicant['school_code'] !== $school_code) {
            return redirect()->route('enroll.step1', ['school_code' => $school->school_code]);
        }

        $emailVerified = (bool) (session('enrollment_email_verified') ?? false);
        if (! $emailVerified) {
            return redirect()->route('enroll.verify', ['school_code' => $school->school_code]);
        }

        $semester = $this->resolveEnrollmentSemester((int) $school->id);
        if ($semester === null) {
            return redirect()->route('school.enroll', ['school_code' => $school->school_code])
                ->with('error', 'No semester is currently available for enrollment.');
        }

        $feeBreakdown = $this->resolveEnrollmentFeeBreakdown(
            schoolId: (int) $school->id,
            semesterId: (int) $semester->id,
            programId: 0,
            isFirstTimeStudent: true,
        );
        $enrollmentFee = (float) ($feeBreakdown['enrollment_fee'] ?? 0.0);

        return view('public.enroll.step4', [
            'school' => $school,
            'semester' => $semester,
            'applicant' => $applicant,
            'enrollmentFee' => $enrollmentFee,
            'tuitionFee' => 0.0,
            'totalFee' => $enrollmentFee,
            'feeSourceLabel' => (string) ($feeBreakdown['source_label'] ?? 'Enrollment Fee Setting'),
            'tuitionSubjectItems' => [],
            'selectedScheduleOptions' => [],
        ]);
    }

    /**
     * Step 4: Complete Enrollment
     */
    public function complete(Request $request, string $school_code): RedirectResponse
    {
        $school = School::query()
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        $applicant = session('enrollment_applicant');
        if ($applicant === null || (($applicant['school_code'] ?? null) !== $school_code)) {
            return redirect()->route('enroll.step1', ['school_code' => $school->school_code]);
        }

        $request->validate([
            'payment_method' => ['required', 'in:gcash,paymaya'],
            'terms_accepted' => ['required', 'accepted'],
        ]);
        $paymentMethod = (string) $request->input('payment_method');

        $emailVerified = (bool) (session('enrollment_email_verified') ?? false);
        if (! $emailVerified) {
            return redirect()->route('enroll.verify', ['school_code' => $school->school_code]);
        }

        $semester = $this->resolveEnrollmentSemester((int) $school->id);
        if ($semester === null) {
            return redirect()->route('school.enroll', ['school_code' => $school->school_code])
                ->with('error', 'No semester is currently available for enrollment.');
        }

        $feeBreakdown = $this->resolveEnrollmentFeeBreakdown(
            schoolId: (int) $school->id,
            semesterId: (int) $semester->id,
            programId: 0,
            isFirstTimeStudent: true,
        );
        $totalFee = (float) ($feeBreakdown['enrollment_fee'] ?? 0.0);

        return redirect()->route('enroll.step4', ['school_code' => $school->school_code])
            ->with('info', 'Please use the Pay with GCash or Pay with Maya button to complete payment via PayMongo.');
    }

    public function verifyEmailForm(string $school_code): View|RedirectResponse
    {
        $school = School::query()->where('school_code', $school_code)->where('status', 'active')->first();
        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }
        $applicant = session('enrollment_applicant');
        if ($applicant === null || $applicant['school_code'] !== $school_code) {
            return redirect()->route('enroll.step1', ['school_code' => $school->school_code]);
        }
        $ver = session('enrollment_email_verification') ?? [];
        return view('public.enroll.verify-email', [
            'school' => $school,
            'applicant' => $applicant,
            'sent_to' => $ver['email'] ?? $applicant['email'] ?? null,
            'expires_at' => isset($ver['expires_at']) ? \Carbon\Carbon::createFromTimestamp($ver['expires_at']) : null,
        ]);
    }

    public function verifyEmailSubmit(Request $request, string $school_code): RedirectResponse
    {
        $school = School::query()->where('school_code', $school_code)->where('status', 'active')->first();
        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }
        $applicant = session('enrollment_applicant');
        if ($applicant === null || $applicant['school_code'] !== $school_code) {
            return redirect()->route('enroll.step1', ['school_code' => $school->school_code]);
        }
        $payload = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);
        $ver = session('enrollment_email_verification');
        if (! is_array($ver) || ($ver['email'] ?? '') !== ($applicant['email'] ?? '')) {
            return back()->withErrors(['code' => 'Verification not initialized.'])->withInput();
        }
        $expired = isset($ver['expires_at']) ? (time() > (int) $ver['expires_at']) : true;
        if ($expired) {
            return back()->withErrors(['code' => 'Code expired. Please resend a new code.'])->withInput();
        }
        if ((string) $ver['code'] !== (string) $payload['code']) {
            return back()->withErrors(['code' => 'Invalid code.'])->withInput();
        }
        $request->session()->put('enrollment_email_verified', true);
        return redirect()->route('enroll.step4', ['school_code' => $school->school_code])->with('status', 'Email verified. You can proceed.');
    }

    public function resendEmailCode(Request $request, string $school_code): RedirectResponse
    {
        $school = School::query()->where('school_code', $school_code)->where('status', 'active')->first();
        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }
        $applicant = session('enrollment_applicant');
        if ($applicant === null || $applicant['school_code'] !== $school_code) {
            return redirect()->route('enroll.step1', ['school_code' => $school->school_code]);
        }
        $code = (string) random_int(100000, 999999);
        $request->session()->put('enrollment_email_verification', [
            'email' => $applicant['email'],
            'code' => $code,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);
        $this->sendBrevoVerification($applicant['email'], $applicant['full_name'], $code);
        return back()->with('status', 'A new code was sent to your email.');
    }

    private function sendBrevoVerification(string $email, string $name, string $code): void
    {
        $apiKey = (string) env('BREVO_API_KEY', '');
        if ($apiKey === '') {
            return;
        }
        $senderEmail = (string) env('BREVO_SENDER_EMAIL', 'no-reply@example.com');
        $senderName = (string) env('BREVO_SENDER_NAME', 'Enrollment Desk');
        $subject = 'Your Enrollment Email Verification Code';
        $text = "Hello {$name},\n\nYour verification code is {$code}.\nThis code will expire in 10 minutes.";
        $payload = [
            'sender' => ['email' => $senderEmail, 'name' => $senderName],
            'to' => [['email' => $email, 'name' => $name]],
            'subject' => $subject,
            'textContent' => $text,
        ];
        try {
            Http::withHeaders(['api-key' => $apiKey])->post('https://api.brevo.com/v3/smtp/email', $payload);
        } catch (\Throwable $e) {
        }
    }
    /**
     * Initiate Stripe Payment Session
     */
    private function initiateStripePayment(Request $request, School $school, float $amount): RedirectResponse
    {
        $applicant = session('enrollment_applicant');
        $email = $applicant['email'] ?? null;
        $fullName = $applicant['full_name'] ?? null;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $productData = [
            'name' => 'Enrollment Fee — ' . $school->name,
            'description' => 'Initial payment for school enrollment verification.',
        ];

        // Use school logo if available
        if (!empty($school->logo_url)) {
            $productData['images'] = [$school->logo_url];
        }

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'customer_email' => $email,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'php',
                    'product_data' => $productData,
                    'unit_amount' => (int) ($amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('enroll.stripe.success', ['school_code' => $school->school_code]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('enroll.step4', ['school_code' => $school->school_code]),
        ]);

        return redirect($session->url);
    }

    /**
     * Handle Stripe Payment Success
     */
    public function stripeSuccess(Request $request, string $school_code): RedirectResponse
    {
        $school = School::query()
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        $applicant = session('enrollment_applicant');
        if ($applicant === null || (($applicant['school_code'] ?? null) !== $school_code)) {
            return redirect()->route('enroll.step1', ['school_code' => $school->school_code]);
        }

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('enroll.step4', ['school_code' => $school_code])->with('error', 'Invalid payment session.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = StripeSession::retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect()->route('enroll.step4', ['school_code' => $school_code])->with('error', 'Payment not completed.');
        }

        $semester = $this->resolveEnrollmentSemester((int) $school->id);
        $feeBreakdown = $this->resolveEnrollmentFeeBreakdown(
            schoolId: (int) $school->id,
            semesterId: (int) $semester->id,
            programId: 0,
            isFirstTimeStudent: true,
        );
        $totalFee = (float) ($feeBreakdown['enrollment_fee'] ?? 0.0);
        $passwordHash = $applicant['password_hash'];

        $this->finalizeEnrollment($school, $semester, $applicant, $totalFee, $passwordHash, 'stripe', $session->payment_intent);

        $request->session()->forget(['enrollment_applicant', 'enrollment_courses', 'enrollment_schedule_choices']);

        return redirect()
            ->route('enroll.success', ['school_code' => $school->school_code])
            ->with('mock_payment', [
                'transaction_id' => $session->payment_intent,
                'method' => 'Stripe',
                'status' => 'Paid',
                'paid_at' => now()->format('Y-m-d H:i:s'),
                'amount' => $totalFee,
            ])
            ->with('status', 'Enrollment fee paid successfully via Stripe.');
    }

    /**
     * Initiate PayMongo enrollment payment (GCash or Maya)
     */
    public function initiatePayMongoEnrollment(Request $request, string $school_code): RedirectResponse
    {
        $school = School::query()
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        $applicant = session('enrollment_applicant');
        if ($applicant === null || (($applicant['school_code'] ?? null) !== $school_code)) {
            return redirect()->route('enroll.step1', ['school_code' => $school->school_code]);
        }

        $request->validate([
            'wallet' => ['required', 'in:gcash,paymaya'],
            'terms_accepted' => ['required', 'accepted'],
        ]);
        $wallet = (string) $request->input('wallet');

        $semester = $this->resolveEnrollmentSemester((int) $school->id);
        if ($semester === null) {
            return redirect()->route('enroll.step4', ['school_code' => $school->school_code])
                ->withErrors(['error' => 'No semester is currently available for enrollment.']);
        }

        $feeBreakdown = $this->resolveEnrollmentFeeBreakdown(
            schoolId: (int) $school->id,
            semesterId: (int) $semester->id,
            programId: 0,
            isFirstTimeStudent: true,
        );
        $totalFee = (float) ($feeBreakdown['enrollment_fee'] ?? 0.0);
        if ($totalFee <= 0) {
            return redirect()->route('enroll.step4', ['school_code' => $school->school_code])
                ->withErrors(['error' => 'Invalid enrollment fee.']);
        }

        $name = $applicant['full_name'] ?? 'Applicant';
        $email = $applicant['email'] ?? '';
        $rawPhone = $applicant['phone'] ?? '09171234567';
        $phoneDigits = preg_replace('/\D/', '', $rawPhone);
        if (strlen($phoneDigits) === 10 && str_starts_with($phoneDigits, '9')) {
            $phone = '+63' . $phoneDigits;
        } elseif (strlen($phoneDigits) === 11 && str_starts_with($phoneDigits, '09')) {
            $phone = '+63' . substr($phoneDigits, 1);
        } elseif (strlen($phoneDigits) === 12 && str_starts_with($phoneDigits, '63')) {
            $phone = '+' . $phoneDigits;
        } else {
            $phone = '+639171234567';
        }

        $returnUrl = route('enroll.paymongo.success', ['school_code' => $school->school_code]);

        try {
            $amountCentavos = (int) round($totalFee * 100);
            $description = 'Enrollment Fee — ' . $school->name;

            $intentResp = $this->payMongoService->createPaymentIntent($amountCentavos, $description);
            $intentId = $intentResp['data']['id'] ?? null;
            if (! $intentId) {
                throw new \RuntimeException('PayMongo did not return a payment intent ID.');
            }

            $request->session()->put('paymongo_enrollment_intent_' . (int) $school->id, $intentId);
            $request->session()->put('paymongo_enrollment_wallet_' . (int) $school->id, $wallet);

            $pmResp = $this->payMongoService->createEwalletPaymentMethod($wallet, $name, $email, $phone);
            $pmId = $pmResp['data']['id'] ?? null;
            if (! $pmId) {
                throw new \RuntimeException('PayMongo did not return a payment method ID.');
            }

            $attach = $this->payMongoService->attachPaymentMethod($intentId, $pmId, $returnUrl);
            $attrs = $attach['data']['attributes'] ?? [];
            $status = $attrs['status'] ?? '';

            if ($status === 'succeeded') {
                $request->session()->forget('paymongo_enrollment_intent_' . (int) $school->id);
                $passwordHash = $applicant['password_hash'] ?? null;
                if ($passwordHash === null && isset($applicant['password']) && is_string($applicant['password']) && $applicant['password'] !== '') {
                    $passwordHash = Hash::make($applicant['password']);
                }
                if ($passwordHash) {
                    $this->finalizeEnrollment($school, $semester, $applicant, $totalFee, $passwordHash, $wallet, $intentId);
                    $request->session()->forget(['enrollment_applicant', 'enrollment_courses', 'enrollment_schedule_choices']);
                    return redirect()
                        ->route('enroll.success', ['school_code' => $school->school_code])
                        ->with('mock_payment', [
                            'transaction_id' => $intentId,
                            'method' => $wallet === 'gcash' ? 'GCash' : 'Maya',
                            'status' => 'Paid',
                            'paid_at' => now()->format('Y-m-d H:i:s'),
                            'amount' => $totalFee,
                        ])
                        ->with('status', 'Enrollment fee paid successfully.');
                }
            }

            $redirectUrl = $attrs['next_action']['redirect']['url'] ?? null;
            if ($redirectUrl) {
                return redirect()->away($redirectUrl);
            }

            throw new \RuntimeException('No redirect URL from PayMongo. Status: ' . $status);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('enroll.step4', ['school_code' => $school->school_code])
                ->withErrors(['error' => 'Payment gateway error: ' . $e->getMessage()]);
        }
    }

    /**
     * PayMongo enrollment payment return (success callback)
     */
    public function paymongoEnrollmentSuccess(Request $request, string $school_code): RedirectResponse
    {
        $school = School::query()
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        $applicant = session('enrollment_applicant');
        if ($applicant === null || (($applicant['school_code'] ?? null) !== $school_code)) {
            return redirect()->route('enroll.step1', ['school_code' => $school->school_code]);
        }

        $intentId = $request->session()->get('paymongo_enrollment_intent_' . (int) $school->id);
        if (! $intentId) {
            return redirect()->route('enroll.step4', ['school_code' => $school_code])
                ->with('info', 'If you already paid, your enrollment is being processed.');
        }

        $totalFee = 0.0;
        try {
            $intent = $this->payMongoService->getPaymentIntent($intentId);
            $status = $intent['data']['attributes']['status'] ?? '';
            if ($status === 'succeeded') {
                $semester = $this->resolveEnrollmentSemester((int) $school->id);
                $feeBreakdown = $this->resolveEnrollmentFeeBreakdown(
                    schoolId: (int) $school->id,
                    semesterId: (int) $semester->id,
                    programId: 0,
                    isFirstTimeStudent: true,
                );
                $totalFee = (float) ($feeBreakdown['enrollment_fee'] ?? 0.0);
                $passwordHash = $applicant['password_hash'] ?? null;
                if ($passwordHash === null && isset($applicant['password']) && is_string($applicant['password']) && $applicant['password'] !== '') {
                    $passwordHash = Hash::make($applicant['password']);
                }
                if ($passwordHash) {
                    $paymentMethod = (string) $request->session()->get('paymongo_enrollment_wallet_' . (int) $school->id, 'gcash');
                    $this->finalizeEnrollment($school, $semester, $applicant, $totalFee, $passwordHash, $paymentMethod, $intentId);
                }
            }
        } finally {
            $request->session()->forget('paymongo_enrollment_intent_' . (int) $school->id);
            $request->session()->forget('paymongo_enrollment_wallet_' . (int) $school->id);
        }

        $request->session()->forget(['enrollment_applicant', 'enrollment_courses', 'enrollment_schedule_choices']);

        return redirect()
            ->route('enroll.success', ['school_code' => $school->school_code])
            ->with('mock_payment', [
                'transaction_id' => $intentId ?? '',
                'method' => 'PayMongo (GCash/Maya)',
                'status' => 'Paid',
                'paid_at' => now()->format('Y-m-d H:i:s'),
                'amount' => $totalFee ?? 0,
            ])
            ->with('status', 'Enrollment fee paid successfully.');
    }

    /**
     * Finalize Enrollment (Create user, billing, payment records)
     */
    private function finalizeEnrollment(School $school, Semester $semester, array $applicant, float $totalFee, string $passwordHash, string $paymentMethod, ?string $transactionId = null): void
    {
        $financeSetting = FinanceSetting::query()->firstOrCreate(
            ['school_id' => (int) $school->id],
            ['auto_approve_payments' => false],
        );
        $autoApprove = (bool) $financeSetting->auto_approve_payments;

        DB::transaction(function () use ($school, $semester, $applicant, $totalFee, $passwordHash, $paymentMethod, $transactionId, $autoApprove): void {
            $user = User::query()->create([
                'full_name'    => $applicant['full_name'],
                'email'        => $applicant['email'],
                'password_hash'=> $passwordHash,
                'birth_date'   => $applicant['birth_date'],
                'gender'       => $applicant['gender'],
                'phone'        => $applicant['phone'] ?? null,
                'address'      => $applicant['address'] ?? null,
                'status'       => 'disabled',
            ]);

            $studentRoleId = DB::table('roles')->where('code', 'student')->value('id');
            if ($studentRoleId !== null) {
                UserRole::query()->create([
                    'user_id'       => $user->id,
                    'school_id'     => $school->id,
                    'role_id'       => $studentRoleId,
                    'is_active'     => true,
                    'assigned_at'   => now(),
                ]);
            }

            $financeUserId = (int) DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.school_id', (int) $school->id)
                ->where('user_roles.is_active', true)
                ->where('roles.code', 'finance_staff')
                ->orderBy('user_roles.id')
                ->value('user_roles.user_id');
            if ($financeUserId <= 0) {
                $financeUserId = (int) $user->id;
            }

            $isStripe = ($paymentMethod === 'stripe');
            $shouldVerify = ($isStripe && $autoApprove);

            $billing = Billing::query()->create([
                'school_id' => (int) $school->id,
                'semester_id' => (int) $semester->id,
                'student_user_id' => (int) $user->id,
                'enrollment_id' => null,
                'billing_rule_id' => null,
                'charge_type' => 'misc_fee',
                'description' => 'Enrollment fee',
                'amount_due' => $totalFee,
                'amount_paid' => $shouldVerify ? $totalFee : 0,
                'payment_status' => $shouldVerify ? 'verified' : 'paid_unverified',
                'clearance_status' => 'not_cleared',
                'generated_by_finance_user_id' => $financeUserId,
            ]);

            Payment::query()->create([
                'school_id' => (int) $school->id,
                'billing_id' => (int) $billing->id,
                'student_user_id' => (int) $user->id,
                'amount' => $totalFee,
                'status' => $shouldVerify ? 'verified' : 'submitted',
                'submitted_at' => now(),
                'verified_at' => $shouldVerify ? now() : null,
                'verified_by_finance_user_id' => $shouldVerify ? $financeUserId : null,
                'remarks' => 'Enrollment fee payment from applicant enrollment flow. Method: ' . $paymentMethod,
                'reference_no' => $transactionId,
            ]);

            $this->auditLogService->log(
                action: 'applicant.enrollment.created',
                entityType: 'user',
                entityId: (int) $user->id,
                metadata: [
                    'school_id'   => $school->id,
                    'school_code' => $school->school_code,
                    'email'       => $user->email,
                    'courses'     => 0,
                    'payment_method' => $paymentMethod,
                    'transaction_id' => $transactionId,
                    'auto_approved' => $shouldVerify,
                ],
                schoolId: (int) $school->id,
                actorUserId: (int) $user->id,
                actorRoleCode: 'student',
            );
        });
    }

    /**
     * Enrollment Success Page
     */
    public function success(string $school_code): View|RedirectResponse
    {
        $school = School::query()
            ->where('school_code', $school_code)
            ->where('status', 'active')
            ->first();

        if ($school === null) {
            return redirect('/')->with('error', 'School not found or not active.');
        }

        return view('public.enroll.success', [
            'school' => $school,
        ]);
    }

    /**
     * Resolve enrollment+tuition fees with priority:
     * 1) semester setting, 2) academic year setting, 3) program setting, 4) default fallback.
     */
    private function resolveEnrollmentFeeBreakdown(int $schoolId, int $semesterId, int $programId, bool $isFirstTimeStudent): array
    {
        $semester = Semester::query()
            ->where('school_id', $schoolId)
            ->find($semesterId);
        $academicYearId = (int) ($semester?->academic_year_id ?? 0);

        $semesterSetting = DB::table('finance_fee_settings')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->where('semester_id', $semesterId)
            ->orderByDesc('id')
            ->first();
        if ($semesterSetting !== null) {
            return [
                'enrollment_fee' => $isFirstTimeStudent ? (float) ($semesterSetting->enrollment_fee ?? 0) : 0.0,
                'tuition_fee' => (float) ($semesterSetting->tuition_fee ?? 0),
                'source_label' => 'Semester Fee Setting',
            ];
        }

        if ($academicYearId > 0) {
            $academicYearSetting = DB::table('finance_fee_settings')
                ->where('school_id', $schoolId)
                ->where('status', 'active')
                ->where('academic_year_id', $academicYearId)
                ->orderByDesc('id')
                ->first();
            if ($academicYearSetting !== null) {
                return [
                    'enrollment_fee' => $isFirstTimeStudent ? (float) ($academicYearSetting->enrollment_fee ?? 0) : 0.0,
                    'tuition_fee' => (float) ($academicYearSetting->tuition_fee ?? 0),
                    'source_label' => 'School Year Fee Setting',
                ];
            }
        }

        if ($programId > 0) {
            $programSetting = DB::table('finance_fee_settings')
                ->where('school_id', $schoolId)
                ->where('status', 'active')
                ->where('program_id', $programId)
                ->orderByDesc('id')
                ->first();
            if ($programSetting !== null) {
                return [
                    'enrollment_fee' => $isFirstTimeStudent ? (float) ($programSetting->enrollment_fee ?? 0) : 0.0,
                    'tuition_fee' => (float) ($programSetting->tuition_fee ?? 0),
                    'source_label' => 'Program Fee Setting',
                ];
            }
        }

        $generalEnrollmentSetting = DB::table('finance_fee_settings')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->whereNull('semester_id')
            ->whereNull('academic_year_id')
            ->whereNull('program_id')
            ->orderByDesc('id')
            ->first();
        if ($generalEnrollmentSetting !== null) {
            return [
                'enrollment_fee' => $isFirstTimeStudent ? (float) ($generalEnrollmentSetting->enrollment_fee ?? 0) : 0.0,
                'tuition_fee' => (float) ($generalEnrollmentSetting->tuition_fee ?? 0),
                'source_label' => 'General Enrollment Fee',
            ];
        }

        return [
            'enrollment_fee' => $isFirstTimeStudent ? 5000.0 : 0.0,
            'tuition_fee' => 5000.0,
            'source_label' => 'Default Fee Setting',
        ];
    }

    private function resolveEnrollmentSemester(int $schoolId): ?Semester
    {
        return Semester::query()
            ->where('school_id', $schoolId)
            ->whereIn('status', ['enrollment_open', 'in_progress'])
            ->orderByRaw("CASE WHEN status = 'enrollment_open' THEN 0 WHEN status = 'in_progress' THEN 1 ELSE 9 END")
            ->orderByDesc('start_date')
            ->first();
    }

    /**
     * Build selectable schedule options by subject for a specific program/year/semester.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildSubjectScheduleRows(int $schoolId, int $semesterId, int $programId, int $yearLevel): array
    {
        $classGroups = ClassGroup::query()
            ->where('school_id', $schoolId)
            ->where('semester_id', $semesterId)
            ->where('program_id', $programId)
            ->where('year_level', $yearLevel)
            ->where('status', '!=', 'archived')
            ->orderBy('name')
            ->get();

        if ($classGroups->isEmpty()) {
            return [];
        }

        $classGroupById = $classGroups->keyBy('id');
        $classGroupIds = $classGroups->pluck('id')->map(fn ($id): int => (int) $id)->all();

        $sessions = ClassSession::query()
            ->with(['subject:id,code,title,units', 'teacher:id,full_name'])
            ->where('school_id', $schoolId)
            ->whereIn('class_group_id', $classGroupIds)
            ->whereIn('status', ['draft', 'locked'])
            ->orderBy('subject_id')
            ->orderBy('class_group_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        if ($sessions->isEmpty()) {
            return [];
        }

        $subjectIds = $sessions
            ->pluck('subject_id')
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $offerings = SubjectOffering::query()
            ->where('school_id', $schoolId)
            ->where('semester_id', $semesterId)
            ->whereIn('subject_id', $subjectIds)
            ->orderByRaw("CASE WHEN status = 'open' THEN 0 WHEN status = 'draft' THEN 1 ELSE 9 END")
            ->orderBy('id')
            ->get()
            ->groupBy('subject_id')
            ->map(fn ($rows) => $rows->first());

        $sections = Section::query()
            ->where('school_id', $schoolId)
            ->whereIn('subject_offering_id', $offerings->pluck('id')->filter()->all())
            ->get();
        $sectionsByOfferingAndIdentifier = [];
        foreach ($sections as $section) {
            $sectionsByOfferingAndIdentifier[(int) $section->subject_offering_id . '|' . (string) $section->identifier] = $section;
        }

        $activeStatuses = ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'];
        $enrollmentCountsBySectionId = Enrollment::query()
            ->where('school_id', $schoolId)
            ->where('semester_id', $semesterId)
            ->whereIn('status', $activeStatuses)
            ->whereIn('section_id', $sections->pluck('id')->all())
            ->select('section_id', DB::raw('COUNT(*) as total'))
            ->groupBy('section_id')
            ->pluck('total', 'section_id')
            ->map(fn ($total): int => (int) $total)
            ->all();

        $rowsBySubject = [];
        foreach ($sessions->groupBy('subject_id') as $subjectId => $subjectSessions) {
            $subjectId = (int) $subjectId;
            $subject = $subjectSessions->first()?->subject;
            if (! $subject) {
                continue;
            }

            $row = [
                'subject_id' => $subjectId,
                'subject_code' => (string) $subject->code,
                'subject_title' => (string) $subject->title,
                'units' => (float) $subject->units,
                'options' => [],
            ];

            foreach ($subjectSessions->groupBy('class_group_id') as $classGroupId => $groupSessions) {
                $classGroupId = (int) $classGroupId;
                /** @var ClassGroup|null $classGroup */
                $classGroup = $classGroupById->get($classGroupId);
                if (! $classGroup) {
                    continue;
                }

                $offering = $offerings->get($subjectId);
                $sectionIdentifier = $this->buildSectionIdentifierForClassGroup($classGroupId);
                $section = $offering
                    ? ($sectionsByOfferingAndIdentifier[(int) $offering->id . '|' . $sectionIdentifier] ?? null)
                    : null;
                $enrolledCount = $section ? (int) ($enrollmentCountsBySectionId[(int) $section->id] ?? 0) : 0;
                $capacity = max((int) ($classGroup->student_capacity ?? 0), 1);
                $remaining = max($capacity - $enrolledCount, 0);

                $sessionBlocks = $groupSessions->map(function (ClassSession $session): array {
                    $start = (string) substr((string) $session->start_time, 0, 5);
                    $end = (string) substr((string) $session->end_time, 0, 5);
                    $dayOfWeek = (int) $session->day_of_week;
                    $dayLabel = match ($dayOfWeek) {
                        1 => 'Mon',
                        2 => 'Tue',
                        3 => 'Wed',
                        4 => 'Thu',
                        5 => 'Fri',
                        6 => 'Sat',
                        7 => 'Sun',
                        default => 'Day ' . $dayOfWeek,
                    };

                    return [
                        'day_of_week' => $dayOfWeek,
                        'day' => $dayLabel,
                        'start_time' => $start,
                        'end_time' => $end,
                        'start_min' => ((int) substr($start, 0, 2) * 60) + (int) substr($start, 3, 2),
                        'end_min' => ((int) substr($end, 0, 2) * 60) + (int) substr($end, 3, 2),
                        'teacher_name' => (string) ($session->teacher?->full_name ?? 'TBA'),
                    ];
                })->values()->all();

                $row['options'][] = [
                    'subject_id' => $subjectId,
                    'subject_code' => (string) $subject->code,
                    'subject_title' => (string) $subject->title,
                    'class_group_id' => $classGroupId,
                    'class_group_name' => (string) $classGroup->name,
                    'year_level' => (int) $classGroup->year_level,
                    'capacity' => $capacity,
                    'enrolled_count' => $enrolledCount,
                    'remaining' => $remaining,
                    'sessions' => $sessionBlocks,
                ];
            }

            $row['options'] = collect($row['options'])
                ->sortBy(fn (array $option): string => (string) ($option['class_group_name'] ?? ''))
                ->values()
                ->all();

            if ($row['options'] !== []) {
                $rowsBySubject[$subjectId] = $row;
            }
        }

        return collect($rowsBySubject)
            ->sortBy(fn (array $row): string => (string) ($row['subject_code'] ?? ''))
            ->values()
            ->all();
    }

    /**
     * @param array<int, array<string, mixed>> $selectedOptions
     */
    private function detectScheduleConflictMessage(array $selectedOptions): ?string
    {
        $placed = [];
        foreach ($selectedOptions as $option) {
            $subjectCode = (string) ($option['subject_code'] ?? 'SUBJ');
            $classGroupName = (string) ($option['class_group_name'] ?? '?');
            foreach ((array) ($option['sessions'] ?? []) as $session) {
                $day = (int) ($session['day_of_week'] ?? 0);
                $startMin = (int) ($session['start_min'] ?? 0);
                $endMin = (int) ($session['end_min'] ?? 0);
                foreach ($placed as $existing) {
                    if ($day !== (int) $existing['day_of_week']) {
                        continue;
                    }
                    if ($startMin < (int) $existing['end_min'] && $endMin > (int) $existing['start_min']) {
                        return 'Schedule conflict between '
                            . $subjectCode . ' (' . ($session['day'] ?? 'Day') . ' ' . ($session['start_time'] ?? '') . '-' . ($session['end_time'] ?? '') . ', Group ' . $classGroupName . ') and '
                            . $existing['subject_code'] . ' (' . $existing['day'] . ' ' . $existing['start_time'] . '-' . $existing['end_time'] . ', Group ' . $existing['class_group_name'] . ').';
                    }
                }

                $placed[] = [
                    'subject_code' => $subjectCode,
                    'class_group_name' => $classGroupName,
                    'day_of_week' => $day,
                    'day' => (string) ($session['day'] ?? 'Day'),
                    'start_time' => (string) ($session['start_time'] ?? ''),
                    'end_time' => (string) ($session['end_time'] ?? ''),
                    'start_min' => $startMin,
                    'end_min' => $endMin,
                ];
            }
        }

        return null;
    }

    private function buildSectionIdentifierForClassGroup(int $classGroupId): string
    {
        return 'CG-' . $classGroupId;
    }

    private function resolveSystemActorUserId(int $schoolId, int $fallbackUserId): int
    {
        $candidate = (int) DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->whereIn('roles.code', ['school_admin', 'registrar_staff', 'dean', 'finance_staff', 'teacher'])
            ->orderByRaw("CASE roles.code
                WHEN 'school_admin' THEN 0
                WHEN 'registrar_staff' THEN 1
                WHEN 'dean' THEN 2
                WHEN 'finance_staff' THEN 3
                WHEN 'teacher' THEN 4
                ELSE 9 END")
            ->orderBy('user_roles.id')
            ->value('user_roles.user_id');

        return $candidate > 0 ? $candidate : $fallbackUserId;
    }

    /**
     * Resolve tuition by summing each subject's configured price.
     * Priority:
     * 1) Selected offerings subjects, 2) selected class group subjects from generated sessions.
     */
    private function resolveTuitionFromSubjectPrices(int $schoolId, ?ClassGroup $selectedClassGroup, $selectedOfferings): array
    {
        $subjects = collect($selectedOfferings)
            ->map(function ($item) {
                if ($item instanceof Subject) {
                    return $item;
                }
                if ($item instanceof SubjectOffering) {
                    return $item->subject;
                }
                if (is_object($item) && isset($item->subject) && $item->subject instanceof Subject) {
                    return $item->subject;
                }
                return null;
            })
            ->filter()
            ->unique('id')
            ->values();

        if ($subjects->isEmpty() && $selectedClassGroup !== null) {
            $subjectIds = ClassSession::query()
                ->where('school_id', $schoolId)
                ->where('class_group_id', (int) $selectedClassGroup->id)
                ->whereIn('status', ['draft', 'locked'])
                ->pluck('subject_id')
                ->map(fn ($id): int => (int) $id)
                ->filter(fn (int $id): bool => $id > 0)
                ->unique()
                ->values()
                ->all();

            if ($subjectIds !== []) {
                $subjects = Subject::query()
                    ->where('school_id', $schoolId)
                    ->whereIn('id', $subjectIds)
                    ->get();
            }
        }

        $subjectItems = $subjects
            ->map(function (Subject $subject): array {
                return [
                    'id' => (int) $subject->id,
                    'code' => (string) $subject->code,
                    'title' => (string) $subject->title,
                    'price' => (float) ($subject->price_per_subject ?? 0),
                ];
            })
            ->values()
            ->all();

        $tuitionFee = (float) collect($subjectItems)->sum('price');

        return [
            'tuition_fee' => $tuitionFee,
            'subject_items' => $subjectItems,
            'source_label' => 'Subject prices (' . count($subjectItems) . ' subjects)',
        ];
    }
}
