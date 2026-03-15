{{-- Tenant Sidebar Partial (proxy to shared component) --}}
{{-- Usage: @include('tenant.partials.sidebar', ['active' => 'dashboard']) --}}
@include('components.nav.sidebar', ['active' => $active ?? ''])
