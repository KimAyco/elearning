@extends('layouts.app')

@section('title', $quiz->title . ' - Results')

@section('content')
<div class="app-shell lms-dashboard-shell">
    @include('tenant.partials.sidebar', ['active' => 'lms'])

    <div class="main-content">
        <div class="lms-topbar">
            <div class="lms-topbar-left">
                <a href="{{ url('/tenant/classes/'.$quiz->class_group_id.'/'.$quiz->subject_id) }}" class="lms-topbar-menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                <h1 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">{{ $quiz->title }} - Results</h1>
            </div>
        </div>

        <div class="page-body">
            <div class="mdl-layout">
                <main class="mdl-main">
                    <div class="mdl-block">
                        <div class="mdl-block-title">Student Submissions</div>
                        <div style="padding: 0;">
                            @if($attempts->isEmpty())
                                <div style="padding: 40px; text-align: center; color: #94a3b8;">
                                    <p>No students have submitted this quiz yet.</p>
                                </div>
                            @else
                                <div class="lms-table-container">
                                    <table class="lms-table">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Submitted At</th>
                                                <th>Score</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attempts as $attempt)
                                            <tr>
                                                <td style="font-weight: 700; color: #1e293b;">{{ $attempt->student->full_name }}</td>
                                                <td style="color: #64748b;">{{ $attempt->submitted_at?->format('M d, Y h:i A') }}</td>
                                                <td>
                                                    <span style="font-weight: 800; color: #10B981;">{{ $attempt->score }}</span>
                                                    <span style="color: #94a3b8;"> / {{ $attempt->max_score }}</span>
                                                </td>
                                                <td>
                                                    @if($attempt->status === 'graded')
                                                        <span class="lms-status-pill graded">Graded</span>
                                                    @else
                                                        <span class="lms-status-pill pending">Needs Grading</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn sm primary" style="background: #10B981;" onclick="openGradingModal({{ $attempt->id }})">Review & Grade</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>

<!-- Modal for grading attempts -->
@foreach($attempts as $attempt)
<div id="modal-grade-{{ $attempt->id }}" class="lms-modal">
    <div class="lms-modal-backdrop" onclick="closeGradingModal({{ $attempt->id }})"></div>
    <div class="lms-modal-content" style="max-width: 700px;">
        <div class="lms-modal-header">
            <h2 class="lms-modal-title">Grading: {{ $attempt->student->full_name }}</h2>
            <button type="button" class="lms-modal-close" onclick="closeGradingModal({{ $attempt->id }})">&times;</button>
        </div>
        <div class="lms-modal-body" style="max-height: 70vh; overflow-y: auto; padding: 24px;">
            <form action="{{ url('/tenant/lms/attempts/'.$attempt->id.'/grade') }}" method="POST">
                @csrf
                @foreach($attempt->answers as $answer)
                    <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9;">
                        <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">
                            Question: {{ $answer->question->question_text }} ({{ $answer->question->points }} Points)
                        </div>
                        
                        @if($answer->question->type === 'multiple_choice')
                            <div style="margin-bottom: 12px; font-weight: 600;">
                                Student's Answer: 
                                <span style="{{ $answer->choice?->is_correct ? 'color: #10B981;' : 'color: #ef4444;' }}">
                                    {{ $answer->choice?->choice_text ?? 'No answer' }}
                                </span>
                            </div>
                            <div style="font-size: 0.85rem; color: #94a3b8;">
                                Correct Answer: {{ $answer->question->choices->where('is_correct', true)->first()->choice_text }}
                            </div>
                            <input type="hidden" name="grades[{{ $answer->id }}][answer_id]" value="{{ $answer->id }}">
                            <input type="hidden" name="grades[{{ $answer->id }}][points]" value="{{ $answer->points_awarded }}">
                        @else
                            <div style="background: #f8fafc; padding: 16px; border-radius: 12px; margin-bottom: 12px; color: #334155; line-height: 1.6; border-left: 4px solid #4C97FF;">
                                {!! nl2br(e($answer->essay_answer)) !!}
                            </div>
                            <div class="lms-form-group">
                                <label>Award Points (Max {{ $answer->question->points }})</label>
                                <input type="hidden" name="grades[{{ $answer->id }}][answer_id]" value="{{ $answer->id }}">
                                <input name="grades[{{ $answer->id }}][points]" type="number" step="0.5" max="{{ $answer->question->points }}" min="0" value="{{ $answer->points_awarded ?? 0 }}" class="lms-input">
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="lms-form-group">
                    <label>Overall Feedback (optional)</label>
                    <textarea name="feedback" rows="3" class="lms-input">{{ $attempt->teacher_feedback }}</textarea>
                </div>

                <button type="submit" class="lms-btn-primary" style="width: 100%; padding: 14px; background: #10B981;">
                    Save Grades
                </button>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
/* Reusing common styles */
.lms-dashboard-shell .main-content { background: #f8fafc; min-height: 100vh; }
.lms-topbar { background: #fff; height: 60px; display: flex; align-items: center; padding: 0 24px; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 100; }
.lms-topbar-menu { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: #f1f5f9; color: #64748b; margin-right: 16px; }
.page-body { max-width: 1000px; margin: 0 auto; padding: 32px 20px; }
.mdl-block { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
.mdl-block-title { padding: 12px 24px; background: #fafbff; border-bottom: 1px solid #eef2f7; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }

.lms-table-container { width: 100%; overflow-x: auto; }
.lms-table { width: 100%; border-collapse: collapse; }
.lms-table th { text-align: left; padding: 16px 24px; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; }
.lms-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }

.lms-status-pill { padding: 4px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; }
.lms-status-pill.graded { background: #dcfce7; color: #166534; }
.lms-status-pill.pending { background: #fef9c3; color: #854d0e; }

.lms-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 2000; display: none; align-items: center; justify-content: center; }
.lms-modal.open { display: flex; }
.lms-modal-backdrop { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); }
.lms-modal-content { position: relative; background: #fff; width: 95%; border-radius: 20px; overflow: hidden; animation: modal-slide-up 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes modal-slide-up { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.lms-modal-header { padding: 20px 24px; background: #fafbff; border-bottom: 1px solid #eef2f7; display: flex; align-items: center; justify-content: space-between; }
.lms-modal-title { margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; }
.lms-modal-close { border: none; background: transparent; font-size: 1.5rem; color: #94a3b8; cursor: pointer; }
.lms-input { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; margin-top: 8px; }
.lms-btn-primary { border: none; border-radius: 12px; color: #fff; font-weight: 700; cursor: pointer; }
</style>

<script>
function openGradingModal(id) {
    document.getElementById('modal-grade-' + id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeGradingModal(id) {
    document.getElementById('modal-grade-' + id).classList.remove('open');
    document.body.style.overflow = '';
}
</script>
@endsection
