@extends('superadmin.layout')

@section('title', 'Subscription pricing — Nehemiah Control')

@section('sa_heading', 'Subscription pricing')

@php
    $plans = $paymentPlans ?? collect();
    $planCount = $plans->count();
    $defaultRate = (float) ($platformSettings->price_per_month ?? 1000);
    $longestMonths = $planCount > 0 ? (int) $plans->max('months') : null;
@endphp

@section('sa_content')
    <div class="sa-dash sa-moodle">
        <section class="sa-moodle-region sa-moodle-region--hero">
            <header class="sa-moodle-region-hd">
                <h1 class="sa-moodle-title">Plans &amp; default rate</h1>
                <p class="sa-moodle-summary">
                    Set the platform default per-month amount and publish fixed-term plans. Registrants see these options during school signup and checkout.
                </p>
            </header>
            <nav class="sa-breadcrumb sa-moodle-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('superadmin.dashboard') }}">Super Admin</a>
                <span class="breadcrumb-sep">›</span>
                <span>Pricing</span>
            </nav>
        </section>

        <section class="sa-moodle-region" aria-labelledby="sa-pricing-kpi-heading">
            <header class="sa-moodle-region-hd sa-moodle-region-hd--compact">
                <h2 id="sa-pricing-kpi-heading" class="sa-moodle-region-title">At a glance</h2>
                <p class="sa-moodle-region-desc">Quick reference for what registrants and checkout will use.</p>
            </header>
            <div class="sa-moodle-kpi sa-moodle-kpi--3">
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $planCount }}</span>
                    <span class="sa-moodle-kpi-label">Published plans</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">PHP {{ number_format($defaultRate, 0) }}</span>
                    <span class="sa-moodle-kpi-label">Default / month</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $longestMonths !== null ? $longestMonths . ' mo' : '—' }}</span>
                    <span class="sa-moodle-kpi-label">Longest term</span>
                </div>
            </div>
        </section>

        <div class="sa-pricing-forms-grid">
            <section class="sa-moodle-region sa-moodle-region--pricing-rate" aria-labelledby="sa-pricing-default-heading">
                <header class="sa-moodle-region-hd">
                    <h2 id="sa-pricing-default-heading" class="sa-moodle-region-title">Default monthly price</h2>
                    <p class="sa-moodle-region-desc">Fallback rate when a plan does not override price per month.</p>
                </header>
                <div class="sa-pricing-form-wrap">
                    <form method="post" action="{{ url('/superadmin/pricing/platform') }}" class="sa-pricing-form">
                        @csrf
                        <div class="form-group">
                            <label for="price_per_month_default">Price per month (PHP)</label>
                            <input id="price_per_month_default" type="number" min="0" step="0.01" name="price_per_month" value="{{ old('price_per_month', number_format($defaultRate, 2, '.', '')) }}" required inputmode="decimal">
                        </div>
                        <button class="btn primary sm" type="submit">Save default price</button>
                    </form>
                </div>
            </section>

            <section class="sa-moodle-region sa-moodle-region--pricing-add" aria-labelledby="sa-pricing-add-heading">
                <header class="sa-moodle-region-hd">
                    <h2 id="sa-pricing-add-heading" class="sa-moodle-region-title">Add subscription plan</h2>
                    <p class="sa-moodle-region-desc">Create a named bundle (e.g. 12 months) with its own monthly and total pricing.</p>
                </header>
                <div class="sa-pricing-form-wrap">
                    <form method="post" action="{{ url('/superadmin/pricing/plans') }}" class="sa-pricing-form">
                        @csrf
                        <div class="sa-pricing-form-grid">
                            <div class="form-group">
                                <label for="new_plan_name">Plan name</label>
                                <input id="new_plan_name" name="name" type="text" placeholder="Plan 12 months" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="new_plan_months">Months</label>
                                <input id="new_plan_months" type="number" min="1" max="120" name="months" required inputmode="numeric">
                            </div>
                            <div class="form-group">
                                <label for="new_plan_ppm">Price / month</label>
                                <input id="new_plan_ppm" type="number" min="0" step="0.01" name="price_per_month" required inputmode="decimal">
                            </div>
                            <div class="form-group">
                                <label for="new_plan_total">Total price (optional)</label>
                                <input id="new_plan_total" type="number" min="0" step="0.01" name="total_price" inputmode="decimal">
                            </div>
                        </div>
                        <button class="btn success sm" type="submit">Add plan</button>
                    </form>
                </div>
            </section>
        </div>

        <section class="sa-moodle-region sa-moodle-region--pricing-table" aria-labelledby="sa-pricing-table-heading">
            <header class="sa-moodle-region-hd sa-moodle-region-hd--row">
                <div>
                    <h2 id="sa-pricing-table-heading" class="sa-moodle-region-title">Published plans</h2>
                    <p class="sa-moodle-region-desc">Shown on the registration flow. Expand a row to edit fields in place.</p>
                </div>
                <div class="sa-moodle-pill-strip" aria-live="polite">
                    @if ($planCount > 0)
                        <span class="sa-moodle-pill sa-moodle-pill--green">{{ $planCount }} active</span>
                    @else
                        <span class="sa-moodle-pill sa-moodle-pill--slate">No plans yet</span>
                    @endif
                </div>
            </header>

            <div class="sa-approval-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Plan</th>
                            <th scope="col">Months</th>
                            <th scope="col">Price/month</th>
                            <th scope="col">Total</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr>
                                <td><span class="sa-pricing-plan-name">{{ $plan->name }}</span></td>
                                <td><span class="sa-pricing-mono">{{ (int) $plan->months }}</span></td>
                                <td><span class="sa-pricing-mono">PHP {{ number_format((float) $plan->price_per_month, 2) }}</span></td>
                                <td><span class="sa-pricing-mono">PHP {{ number_format((float) ($plan->total_price ?? ((float) $plan->price_per_month * (int) $plan->months)), 2) }}</span></td>
                                <td>
                                    <details class="sa-pricing-details">
                                        <summary>
                                            <span>Edit</span>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <polygon points="5 3 19 12 5 21 5 3"/>
                                            </svg>
                                        </summary>
                                        <div class="sa-pricing-plan-edit">
                                            <form method="post" action="{{ url('/superadmin/pricing/plans') }}">
                                                @csrf
                                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                <div class="sa-pricing-edit-grid">
                                                    <input name="name" value="{{ $plan->name }}" required aria-label="Plan name">
                                                    <input type="number" min="1" max="120" name="months" value="{{ (int) $plan->months }}" required aria-label="Months">
                                                    <input type="number" min="0" step="0.01" name="price_per_month" value="{{ number_format((float) $plan->price_per_month, 2, '.', '') }}" required aria-label="Price per month">
                                                    <input type="number" min="0" step="0.01" name="total_price" value="{{ number_format((float) ($plan->total_price ?? ((float) $plan->price_per_month * (int) $plan->months)), 2, '.', '') }}" aria-label="Total price">
                                                </div>
                                                <button class="btn primary sm" type="submit">Update plan</button>
                                            </form>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="sa-approval-empty" role="status">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                        <p>No subscription plans configured yet. Add one using the form above.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="sa-moodle-region sa-moodle-region--footer" aria-labelledby="sa-pricing-foot-heading">
            <header class="sa-moodle-region-hd">
                <h2 id="sa-pricing-foot-heading" class="sa-moodle-region-title">Registration &amp; checkout</h2>
            </header>
            <p class="sa-moodle-footnote">
                The <strong>default monthly price</strong> applies when you need a single baseline across the platform.
                Each <strong>published plan</strong> can advertise a different term length and total; registrants pick from this list during school signup.
                Edits here take effect on the next page load of the public registration flow.
            </p>
        </section>
    </div>
@endsection
