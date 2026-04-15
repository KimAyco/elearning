@extends('superadmin.layout')

@section('title', 'Dashboard — Nehemiah Control')

@section('sa_heading', 'Dashboard')

@section('sa_content')
    {{-- Moodle-style: region blocks + coursebox-style tiles --}}
    <div class="sa-dash sa-moodle">
        <section class="sa-moodle-region sa-moodle-region--hero">
            <header class="sa-moodle-region-hd">
                <h1 class="sa-moodle-title">Platform dashboard</h1>
                <p class="sa-moodle-summary">Tenant health, onboarding pipeline, and new school volume. Jump to a work area or review analytics below.</p>
            </header>
            <nav class="sa-breadcrumb sa-moodle-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('superadmin.dashboard') }}">Super Admin</a>
                <span class="breadcrumb-sep">›</span>
                <span>Dashboard</span>
            </nav>
        </section>

        <div class="sa-moodle-tiles" role="navigation" aria-label="Quick access">
            <a href="{{ route('superadmin.approvals') }}" class="sa-moodle-tile sa-moodle-tile--amber">
                <div class="sa-moodle-tile-ic" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div class="sa-moodle-tile-body">
                    <h2 class="sa-moodle-tile-name">Queue snapshot</h2>
                    <p class="sa-moodle-tile-text">Counts include both unpaid and paid rows still in this pipeline.</p>
                    @if($pendingRegistrations->count() > 0)
                        <span class="sa-moodle-tile-meta">{{ $pendingRegistrations->count() }} in queue</span>
                    @else
                        <span class="sa-moodle-tile-meta sa-moodle-tile-meta--muted">Queue clear</span>
                    @endif
                </div>
            </a>
            <a href="{{ route('superadmin.pricing') }}" class="sa-moodle-tile sa-moodle-tile--purple">
                <div class="sa-moodle-tile-ic" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="sa-moodle-tile-body">
                    <h2 class="sa-moodle-tile-name">At a glance</h2>
                    <p class="sa-moodle-tile-text">Quick reference for what registrants and checkout will use.</p>
                    <span class="sa-moodle-tile-meta">{{ $paymentPlans->count() }} plans</span>
                </div>
            </a>
            <a href="{{ route('superadmin.schools') }}" class="sa-moodle-tile sa-moodle-tile--blue">
                <div class="sa-moodle-tile-ic" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div class="sa-moodle-tile-body">
                    <h2 class="sa-moodle-tile-name">Directory snapshot</h2>
                    <p class="sa-moodle-tile-text">Roll-up of tenants on the platform right now.</p>
                    <span class="sa-moodle-tile-meta">{{ $schools->count() }} schools</span>
                </div>
            </a>
        </div>

        <section class="sa-moodle-region" aria-labelledby="sa-kpi-heading">
            <header class="sa-moodle-region-hd sa-moodle-region-hd--compact">
                <h2 id="sa-kpi-heading" class="sa-moodle-region-title">Key indicators</h2>
                <p class="sa-moodle-region-desc">Roll-up of schools, access state, and trial volume.</p>
            </header>
            <div class="sa-moodle-kpi">
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $schools->count() }}</span>
                    <span class="sa-moodle-kpi-label">Total schools</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $schools->where('status','active')->count() }}</span>
                    <span class="sa-moodle-kpi-label">Active access</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $schools->where('status','suspended')->count() }}</span>
                    <span class="sa-moodle-kpi-label">Suspended</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $schools->where('subscription_state','trial')->count() }}</span>
                    <span class="sa-moodle-kpi-label">On trial</span>
                </div>
            </div>
        </section>

        <div class="sa-moodle-charts">
            <section class="sa-moodle-region sa-moodle-chart-card" aria-labelledby="sa-chart-sub-heading">
                <header class="sa-moodle-region-hd">
                    <h2 id="sa-chart-sub-heading" class="sa-moodle-region-title">Schools by subscription state</h2>
                    <p class="sa-moodle-region-desc">Where revenue is in trial vs active billing vs risk.</p>
                </header>
                <div class="sa-chart-canvas-wrap">
                    <canvas id="saChartSubscription" height="240" aria-label="Doughnut chart of subscription states"></canvas>
                </div>
            </section>

            <section class="sa-moodle-region sa-moodle-chart-card" aria-labelledby="sa-chart-mo-heading">
                <header class="sa-moodle-region-hd">
                    <h2 id="sa-chart-mo-heading" class="sa-moodle-region-title">New schools (last 6 months)</h2>
                    <p class="sa-moodle-region-desc">Onboarding volume — spot spikes after campaigns or policy changes.</p>
                </header>
                <div class="sa-chart-canvas-wrap">
                    <canvas id="saChartMonthly" height="240" aria-label="Line chart of schools added per month"></canvas>
                </div>
            </section>
        </div>

        <section class="sa-moodle-region sa-moodle-region--footer" aria-labelledby="sa-ops-heading">
            <header class="sa-moodle-region-hd">
                <h2 id="sa-ops-heading" class="sa-moodle-region-title">How this area fits together</h2>
            </header>
            <p class="sa-moodle-footnote">
                Use <strong>Approvals</strong> to turn paid registrations into live tenants, <strong>Subscription pricing</strong> to control what registrants see at checkout,
                and <strong>Schools</strong> for day‑to‑day access control. Charts above summarize current subscription mix and recent signup cadence.
            </p>
        </section>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const cfg = {
        subscription: {
            labels: @json($chartSubscriptionLabels), data: @json($chartSubscriptionData)
        },
        monthly: {
            labels: @json($chartMonthlyLabels), data: @json($chartMonthlyData)
        }
    };

    const palette = ['#c55a2e', '#7c3aed', '#2563eb', '#f59e0b', '#64748b', '#0d9488'];

    function init() {
        if (typeof Chart === 'undefined') return;

        const elSub = document.getElementById('saChartSubscription');
        const elMo = document.getElementById('saChartMonthly');
        if (!elSub || !elMo) return;

        new Chart(elSub, {
            type: 'doughnut',
            data: {
                labels: cfg.subscription.labels,
                datasets: [{
                    data: cfg.subscription.data,
                    backgroundColor: cfg.subscription.labels[0] === 'No tenants yet'
                        ? ['#e2e8f0']
                        : palette.slice(0, cfg.subscription.data.length),
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 11 }, padding: 12, usePointStyle: true },
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const v = ctx.parsed;
                                const pct = total ? Math.round((v / total) * 100) : 0;
                                return ' ' + ctx.label + ': ' + v + ' (' + pct + '%)';
                            },
                        },
                    },
                },
                cutout: '58%',
            },
        });

        new Chart(elMo, {
            type: 'line',
            data: {
                labels: cfg.monthly.labels,
                datasets: [{
                    label: 'New schools',
                    data: cfg.monthly.data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: (v) => (Number.isInteger(v) ? v : ''),
                        },
                        grid: { color: 'rgba(148, 163, 184, 0.25)' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
                plugins: {
                    legend: { display: false },
                },
            },
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endpush
