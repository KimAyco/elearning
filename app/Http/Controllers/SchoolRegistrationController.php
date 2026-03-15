<?php

namespace App\Http\Controllers;

use App\Models\SchoolRegistration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SchoolRegistrationController extends Controller
{
    public function showForm(): View
    {
        return view('register.school_form', [
            'plans' => $this->availablePlans(),
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'subdomain' => ['nullable', 'string', 'max:60', 'regex:/^[a-z0-9][a-z0-9-]*$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'plan_months' => ['required', 'integer', 'min:1'],
        ]);

        $emailExistsInUsers = DB::table('users')
            ->whereRaw('LOWER(email) = ?', [Str::lower($payload['email'])])
            ->exists();
        if ($emailExistsInUsers) {
            return back()->withErrors(['email' => 'Email is already used by an existing account.'])->withInput();
        }

        $hasOpenRegistration = SchoolRegistration::query()
            ->whereRaw('LOWER(email) = ?', [Str::lower($payload['email'])])
            ->whereIn('status', ['pending', 'paid'])
            ->exists();
        if ($hasOpenRegistration) {
            return back()->withErrors(['email' => 'A registration for this email is already pending activation.'])->withInput();
        }

        $plans = $this->availablePlans();
        $selectedPlan = $plans->firstWhere('months', (int) $payload['plan_months']);
        if ($selectedPlan === null) {
            return back()->withErrors(['plan_months' => 'Selected subscription plan is not available.'])->withInput();
        }

        $paymentToken = Str::random(48);
        $verificationCode = (string) random_int(100000, 999999);
        
        $registration = SchoolRegistration::query()->create([
            'name' => $payload['name'],
            'email' => Str::lower($payload['email']),
            'phone' => $payload['phone'] ?? null,
            'address' => $payload['address'] ?? null,
            'subdomain' => isset($payload['subdomain']) && $payload['subdomain'] !== ''
                ? Str::lower($payload['subdomain'])
                : null,
            'plan_months' => (int) $selectedPlan->months,
            'status' => 'pending',
            'auto_approved' => false,
            'metadata' => [
                'admin_password_hash' => Hash::make($payload['password']),
                'plan_name' => $selectedPlan->name,
                'price_per_month' => (float) $selectedPlan->price_per_month,
                'total_price' => (float) $selectedPlan->total_price,
                'payment_token' => $paymentToken,
                'verification_code' => $verificationCode,
                'verification_expires_at' => now()->addMinutes(10)->timestamp,
                'email_verified' => false,
            ],
        ]);

        $this->sendBrevoVerification($payload['email'], $payload['name'], $verificationCode);

        return redirect()->route('register.school.verify', [
            'registration' => $registration->id,
            'token' => $paymentToken,
        ])->with('status', 'We sent a verification code to your email.');
    }

    public function verifyEmailForm(Request $request, SchoolRegistration $registration): View
    {
        $this->assertPaymentAccess($request, $registration);

        $meta = (array) ($registration->metadata ?? []);
        $expiresAt = isset($meta['verification_expires_at']) ? \Carbon\Carbon::createFromTimestamp($meta['verification_expires_at']) : null;

        return view('register.verify-email', [
            'registration' => $registration,
            'sent_to' => $registration->email,
            'expires_at' => $expiresAt,
        ]);
    }

    public function verifyEmailSubmit(Request $request, SchoolRegistration $registration): RedirectResponse
    {
        $this->assertPaymentAccess($request, $registration);

        $payload = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $meta = (array) ($registration->metadata ?? []);
        
        $expired = isset($meta['verification_expires_at']) ? (time() > (int) $meta['verification_expires_at']) : true;
        if ($expired) {
            return back()->withErrors(['code' => 'Code expired. Please resend a new code.'])->withInput();
        }

        if ((string) ($meta['verification_code'] ?? '') !== (string) $payload['code']) {
            return back()->withErrors(['code' => 'Invalid code.'])->withInput();
        }

        $meta['email_verified'] = true;
        $registration->metadata = $meta;
        $registration->save();

        return redirect()->route('register.school.payment', [
            'registration' => $registration->id,
            'token' => $request->query('token', $request->input('token', '')),
        ])->with('status', 'Email verified. You can proceed with payment.');
    }

    public function resendEmailCode(Request $request, SchoolRegistration $registration): RedirectResponse
    {
        $this->assertPaymentAccess($request, $registration);

        $code = (string) random_int(100000, 999999);
        $meta = (array) ($registration->metadata ?? []);
        $meta['verification_code'] = $code;
        $meta['verification_expires_at'] = now()->addMinutes(10)->timestamp;
        $registration->metadata = $meta;
        $registration->save();

        $this->sendBrevoVerification($registration->email, $registration->name, $code);

        return back()->with('status', 'A new code was sent to your email.');
    }

    public function payment(Request $request, SchoolRegistration $registration): View|RedirectResponse
    {
        $this->assertPaymentAccess($request, $registration);

        $meta = (array) ($registration->metadata ?? []);
        $emailVerified = (bool) ($meta['email_verified'] ?? false);
        
        if (! $emailVerified) {
            return redirect()->route('register.school.verify', [
                'registration' => $registration->id,
                'token' => $request->query('token', ''),
            ]);
        }

        $plans = $this->availablePlans();
        $plan = $plans->firstWhere('months', (int) $registration->plan_months) ?? (object) [
            'name' => ($registration->metadata['plan_name'] ?? 'Custom Plan'),
            'months' => (int) $registration->plan_months,
            'price_per_month' => (float) ($registration->metadata['price_per_month'] ?? 0),
            'total_price' => (float) ($registration->metadata['total_price'] ?? 0),
        ];

        return view('register.payment', [
            'registration' => $registration,
            'plan' => $plan,
        ]);
    }

    public function confirmPayment(Request $request, SchoolRegistration $registration): RedirectResponse
    {
        $this->assertPaymentAccess($request, $registration);

        $payload = $request->validate([
            'payment_method' => ['required', 'in:gcash,paymaya'],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'mock_confirm' => ['required', 'accepted'],
        ]);

        if ($registration->status === 'approved') {
            return redirect('/')->with('status', 'This school registration is already approved and active.');
        }

        if ($registration->status !== 'paid') {
            $meta = (array) ($registration->metadata ?? []);
            $meta['payment_mode'] = 'mock';
            $meta['payment_method'] = $payload['payment_method'];
            $meta['payment_reference'] = $payload['payment_reference'] ?? null;
            $meta['paid_at'] = now()->toDateTimeString();

            $registration->status = 'paid';
            $registration->metadata = $meta;
            $registration->save();
        }

        return redirect('/')
            ->with('status', 'Mock payment completed. Your school is now waiting for super admin activation.');
    }

    private function assertPaymentAccess(Request $request, SchoolRegistration $registration): void
    {
        $token = (string) $request->query('token', $request->input('token', ''));
        $expected = (string) ($registration->metadata['payment_token'] ?? '');

        abort_unless($token !== '' && $expected !== '' && hash_equals($expected, $token), 403);
    }

    private function availablePlans(): Collection
    {
        $plans = DB::table('payment_plans')
            ->select(['name', 'months', 'price_per_month', 'total_price'])
            ->orderBy('months')
            ->get()
            ->map(function ($plan) {
                $total = $plan->total_price !== null
                    ? (float) $plan->total_price
                    : ((float) $plan->price_per_month * (int) $plan->months);

                return (object) [
                    'name' => (string) $plan->name,
                    'months' => (int) $plan->months,
                    'price_per_month' => (float) $plan->price_per_month,
                    'total_price' => $total,
                ];
            });

        if ($plans->isNotEmpty()) {
            return $plans->values();
        }

        $pricePerMonth = (float) (DB::table('platform_settings')->value('price_per_month') ?? 1500);

        return collect([3, 6, 12])->map(function (int $months) use ($pricePerMonth): object {
            return (object) [
                'name' => $months . '-Month Plan',
                'months' => $months,
                'price_per_month' => $pricePerMonth,
                'total_price' => $pricePerMonth * $months,
            ];
        });
    }

    private function sendBrevoVerification(string $email, string $name, string $code): void
    {
        $apiKey = (string) env('BREVO_API_KEY', '');
        if ($apiKey === '') {
            return;
        }
        $senderEmail = (string) env('BREVO_SENDER_EMAIL', 'no-reply@example.com');
        $senderName = (string) env('BREVO_SENDER_NAME', 'School Registration');
        $subject = 'Your School Registration Email Verification Code';
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
}
