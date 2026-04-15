{{-- Teacher-only: edit/delete section (LmsLesson). Requires $lesson. --}}
@php
    $lessonUrl = url('/tenant/lms/lessons/' . $lesson->id);
@endphp
<div class="mdl-section-head-tools">
    <button
        type="button"
        class="mdl-icon-btn mdl-icon-btn--drag"
        title="Drag to reorder sections"
        aria-label="Drag to reorder sections"
        data-drag-lesson-handle
        data-lesson-id="{{ $lesson->id }}"
        draggable="true"
    >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M9 4h.01"/>
            <path d="M9 12h.01"/>
            <path d="M9 20h.01"/>
            <path d="M15 4h.01"/>
            <path d="M15 12h.01"/>
            <path d="M15 20h.01"/>
        </svg>
    </button>
    <button
        type="button"
        class="mdl-icon-btn"
        title="Manage section and its uploads"
        aria-label="Manage section and its uploads"
        data-manage-section
        data-lesson-id="{{ $lesson->id }}"
        data-lesson-title="{{ e($lesson->title) }}"
    >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
    </button>
    <form
        method="post"
        action="{{ $lessonUrl }}"
        class="mdl-icon-form"
        data-confirm-delete
        data-confirm-title="Delete this section?"
        data-confirm-message="Resources inside it will move to ungrouped. Quizzes in this section stay in the course but are no longer grouped under it."
    >
        @csrf
        @method('DELETE')
        <button type="submit" class="mdl-icon-btn mdl-icon-btn-danger" title="Delete section" aria-label="Delete section">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                <line x1="10" y1="11" x2="10" y2="17"/>
                <line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
        </button>
    </form>
</div>
