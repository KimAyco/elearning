{{-- Teacher-only: edit/delete controls on LMS resource cards (requires $m = LmsModule) --}}
@php
    $moduleUrl = url('/tenant/lms/modules/' . $m->id);
@endphp
<div class="mdl-resource-manage-icons">
    <button
        type="button"
        class="mdl-icon-btn"
        title="Edit resource"
        aria-label="Edit resource"
        data-edit-module
        data-module-id="{{ $m->id }}"
        data-module-title="{{ e($m->title) }}"
        data-module-description="{{ e($m->description ?? '') }}"
        data-module-type="{{ e($m->type) }}"
        data-module-content="{{ e($m->content ?? '') }}"
    >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
    </button>
    <form
        method="post"
        action="{{ $moduleUrl }}"
        class="mdl-icon-form"
        data-confirm-delete
        data-confirm-title="Delete this resource?"
        data-confirm-message="This cannot be undone."
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="mdl-icon-btn mdl-icon-btn-danger" title="Delete resource" aria-label="Delete resource">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                <line x1="10" y1="11" x2="10" y2="17"/>
                <line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
        </button>
    </form>
</div>
