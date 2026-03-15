@extends('layouts.app')

@section('title', $quiz->title . ' - Quiz')

@section('content')
<div class="app-shell lms-dashboard-shell">
    @include('tenant.partials.sidebar', ['active' => 'class'])

    <div class="main-content">
        <div class="lms-topbar">
            <div class="lms-topbar-left">
                <a href="{{ url('/tenant/classes/'.$quiz->class_group_id.'/'.$quiz->subject_id) }}" class="lms-topbar-menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                <h1 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">{{ $quiz->title }}</h1>
            </div>
            @if($quiz->time_limit_minutes && $attempt)
            <div style="margin-left:auto; display:flex; align-items:center; gap:10px;">
                <span style="font-size:0.8rem; color:#64748b; font-weight:700;">Time Remaining</span>
                <div id="quiz-timer" style="min-width:110px; text-align:center; padding:8px 12px; border-radius:10px; background:#0f172a; color:#fff; font-weight:800;">00:00:00</div>
            </div>
            @endif
        </div>

        <div class="page-body">
            <div class="mdl-layout">
                <main class="mdl-main">
                    @if(!$attempt)
                        {{-- START QUIZ STATE --}}
                        <div class="mdl-block">
                            <div class="mdl-block-title">Quiz Instructions</div>
                            <div style="padding: 32px; color: #475569; line-height: 1.6; text-align: center;">
                                <div style="margin-bottom: 24px; font-size: 1.1rem;">
                                    {!! nl2br(e($quiz->description)) !!}
                                </div>
                                <div style="display: flex; justify-content: center; gap: 24px; margin-bottom: 40px;">
                                    <div style="background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; min-width: 140px;">
                                        <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Questions</div>
                                        <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $questions->count() }}</div>
                                    </div>
                                    @if($quiz->time_limit_minutes)
                                    <div style="background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; min-width: 140px;">
                                        <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Time Limit</div>
                                        <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $quiz->time_limit_minutes }}m</div>
                                    </div>
                                    @endif
                                </div>
                                
                                <form action="{{ url('/tenant/lms/quizzes/'.$quiz->id.'/start') }}" method="POST" id="start-quiz-form">
                                    @csrf
                                    <button type="button" class="lms-btn-primary" style="padding: 16px 60px; font-size: 1.1rem; background: #10B981;" onclick="confirmStart()">
                                        Start Quiz
                                    </button>
                                </form>
                                <p style="margin-top: 16px; font-size: 0.85rem; color: #94a3b8;">Once you start, the timer cannot be paused.</p>
                            </div>
                        </div>
                    @else
                        {{-- IN PROGRESS STATE --}}
                        <form action="{{ url('/tenant/lms/quizzes/'.$quiz->id.'/submit') }}" method="POST" id="quiz-form">
                            @csrf
                            <input type="hidden" name="answers[__seed]" value="">
                            @foreach($questions as $index => $q)
                            <div class="mdl-block" style="margin-top: 24px;">
                                <div class="mdl-block-title" style="display: flex; justify-content: space-between;">
                                    <span>Question {{ $index + 1 }}</span>
                                    <span style="font-size: 0.75rem; color: #94a3b8;">{{ $q->points }} Points</span>
                                </div>
                                <div style="padding: 24px;">
                                    <p style="font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 20px;">{{ $q->question_text }}</p>
                                    
                                    @if($q->type === 'multiple_choice')
                                        <div style="display: flex; flex-direction: column; gap: 12px;">
                                            @foreach($q->choices as $choice)
                                            <label class="quiz-option">
                                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $choice->id }}">
                                                <div class="quiz-option-card">
                                                    <div class="quiz-option-radio"></div>
                                                    <span>{{ $choice->choice_text }}</span>
                                                </div>
                                            </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <textarea name="answers[{{ $q->id }}]" rows="6" class="lms-input" placeholder="Type your essay answer here..."></textarea>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                            <div style="margin-top: 32px; display: flex; justify-content: flex-end;">
                                <button type="button" class="lms-btn-primary" style="padding: 14px 40px; font-size: 1rem; background: #10B981;" onclick="confirmSubmit()">
                                    Finish Quiz
                                </button>
                            </div>
                        </form>
                    @endif
                </main>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modals --}}
<div id="quiz-modal" class="lms-modal">
    <div class="lms-modal-backdrop" onclick="closeModal()"></div>
    <div class="lms-modal-content" style="max-width: 600px;">
        <div class="lms-modal-header">
            <h2 class="lms-modal-title" id="modal-title">Review Answers</h2>
            <button type="button" class="lms-modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="lms-modal-body" id="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
            {{-- Content injected by JS --}}
        </div>
        <div class="lms-modal-footer" style="padding: 16px 24px; border-top: 1px solid #eef2f7; display: flex; gap: 12px; justify-content: flex-end;">
            <button type="button" class="lms-btn-ghost" onclick="closeModal()" style="padding: 10px 20px; border: 1.5px solid #e2e8f0; background: #fff; color: #64748b; border-radius: 10px; font-weight: 700; cursor: pointer;">Cancel</button>
            <button type="button" id="modal-confirm-btn" class="lms-btn-primary" style="padding: 10px 24px; background: #10B981;">Confirm</button>
        </div>
    </div>
</div>

<style>
.lms-dashboard-shell .main-content { background: #f8fafc; min-height: 100vh; }
.lms-topbar { background: #fff; height: 60px; display: flex; align-items: center; padding: 0 24px; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 100; }
.lms-topbar-menu { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: #f1f5f9; color: #64748b; margin-right: 16px; }
.page-body { max-width: 800px; margin: 0 auto; padding: 32px 20px; }
.mdl-block { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
.mdl-block-title { padding: 12px 24px; background: #fafbff; border-bottom: 1px solid #eef2f7; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
.lms-input { width: 100%; padding: 16px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.95rem; resize: vertical; }

.quiz-option { cursor: pointer; }
.quiz-option input { display: none; }
.quiz-option-card { display: flex; align-items: center; gap: 16px; padding: 16px 20px; border: 2px solid #f1f5f9; border-radius: 12px; transition: all 0.2s; }
.quiz-option-radio { width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 50%; position: relative; }
.quiz-option input:checked + .quiz-option-card { border-color: #10B981; background: #f0fdf4; }
.quiz-option input:checked + .quiz-option-card .quiz-option-radio { border-color: #10B981; }
.quiz-option input:checked + .quiz-option-card .quiz-option-radio::after { content: ''; position: absolute; top: 3px; left: 3px; width: 10px; height: 10px; background: #10B981; border-radius: 50%; }
.quiz-option-card span { font-size: 0.95rem; color: #475569; font-weight: 500; }

.lms-btn-primary { border: none; border-radius: 12px; color: #fff; font-weight: 700; cursor: pointer; transition: transform 0.2s; }
.lms-btn-primary:hover { transform: translateY(-2px); filter: brightness(1.05); }

/* Modal styles */
.lms-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px; }
.lms-modal.open { display: flex; }
.lms-modal-backdrop { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); }
.lms-modal-content { background: #fff; width: 100%; border-radius: 20px; position: relative; z-index: 1; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
.lms-modal-header { padding: 16px 24px; border-bottom: 1px solid #eef2f7; display: flex; justify-content: space-between; align-items: center; }
.lms-modal-title { font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; }
.lms-modal-close { background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; }
</style>

<script>
var quizForm = document.getElementById('quiz-form');
var startForm = document.getElementById('start-quiz-form');
var modal = document.getElementById('quiz-modal');
var modalTitle = document.getElementById('modal-title');
var modalBody = document.getElementById('modal-body');
var modalConfirmBtn = document.getElementById('modal-confirm-btn');

function openModal(title, body, confirmText, onConfirm) {
    modalTitle.textContent = title;
    modalBody.innerHTML = body;
    modalConfirmBtn.textContent = confirmText;
    modalConfirmBtn.onclick = onConfirm;
    modal.classList.add('open');
}

function closeModal() {
    modal.classList.remove('open');
}

function confirmStart() {
    openModal(
        'Start Quiz?',
        '<p style="color: #475569; font-size: 1rem;">Are you ready to begin? The timer will start immediately and you cannot pause it.</p>',
        'Yes, Start Now',
        function() {
            startForm.submit();
        }
    );
}

function confirmSubmit() {
    var summary = '<div style="text-align: left;">';
    var unansweredCount = 0;
    
    @if($attempt)
    var questionBlocks = document.querySelectorAll('.mdl-block[style*="margin-top: 24px"]');
    questionBlocks.forEach(function(block, index) {
        var qTitle = block.querySelector('.mdl-block-title span').textContent;
        var qText = block.querySelector('p').textContent;
        var answerText = '<span style="color: #dc2626; font-weight: 600;">Not Answered</span>';
        
        // Check for MC
        var selectedRadio = block.querySelector('input[type="radio"]:checked');
        if (selectedRadio) {
            answerText = '<span style="color: #10B981; font-weight: 600;">' + 
                selectedRadio.closest('.quiz-option').querySelector('span').textContent + 
                '</span>';
        } else {
            // Check for Essay
            var textarea = block.querySelector('textarea');
            if (textarea && textarea.value.trim()) {
                var preview = textarea.value.trim();
                if (preview.length > 60) preview = preview.substring(0, 57) + '...';
                answerText = '<span style="color: #10B981; font-weight: 600;">' + preview + '</span>';
            } else if (textarea) {
                unansweredCount++;
            } else {
                unansweredCount++;
            }
        }

        summary += '<div style="margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">' +
                   '<div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">' + qTitle + '</div>' +
                   '<div style="font-size: 0.9rem; color: #1e293b; margin: 4px 0;">' + qText + '</div>' +
                   '<div style="font-size: 0.95rem;">' + answerText + '</div>' +
                   '</div>';
    });
    @endif

    summary += '</div>';

    var warning = unansweredCount > 0 
        ? '<div style="background: #fff7ed; padding: 16px; border-radius: 12px; border: 1px solid #fed7aa; color: #9a3412; font-size: 0.95rem; margin-bottom: 24px; text-align: center;">' +
          '<strong>Warning:</strong> You have ' + unansweredCount + ' unanswered question(s). ' +
          'Are you sure you want to submit now?</div>'
        : '<div style="background: #f0fdf4; padding: 16px; border-radius: 12px; border: 1px solid #bbf7d0; color: #166534; font-size: 0.95rem; margin-bottom: 24px; text-align: center;">' +
          'All questions have been answered. Please review your choices below.</div>';

    openModal(
        'Review Your Answers',
        warning + summary + '<p style="color: #475569; font-size: 1rem; margin-top: 24px; text-align: center;">Once submitted, your answers cannot be changed.</p>',
        'Submit and Exit Quiz',
        function() {
            finalSubmit();
        }
    );
}

function finalSubmit() {
    if (quizForm) {
        window.removeEventListener('beforeunload', handleAutoSubmit);
        modalConfirmBtn.disabled = true;
        modalConfirmBtn.textContent = 'Submitting...';
        quizForm.dataset.submitted = 'true';
        quizForm.submit();
    }
}

function handleAutoSubmit(e) {
    if (quizForm && quizForm.dataset.submitted !== 'true') {
        // Most browsers won't allow a form submit during beforeunload to be reliable
        // but we'll trigger it anyway.
        quizForm.submit();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    @if($attempt)
        window.addEventListener('beforeunload', handleAutoSubmit);

        var startedAt = new Date("{{ $attempt->started_at->toIso8601String() }}").getTime();
        var limitMins = {{ (int)($quiz->time_limit_minutes ?? 0) }};
        
        if (limitMins > 0) {
            var timerEl = document.getElementById('quiz-timer');
            var format = function (s) {
                var h = Math.floor(s / 3600);
                var m = Math.floor((s % 3600) / 60);
                var sec = s % 60;
                var pad = function (v) { return v < 10 ? '0' + v : '' + v; };
                return pad(h) + ':' + pad(m) + ':' + pad(sec);
            };

            var updateTimer = function() {
                var now = new Date().getTime();
                var elapsedSec = Math.floor((now - startedAt) / 1000);
                var totalLimitSec = limitMins * 60;
                var remaining = totalLimitSec - elapsedSec;

                if (remaining <= 0) {
                    clearInterval(timerIv);
                    if (timerEl) timerEl.textContent = '00:00:00';
                    finalSubmit();
                    return;
                }

                if (timerEl) {
                    timerEl.textContent = format(remaining);
                    if (remaining <= 60) {
                        timerEl.style.background = '#dc2626';
                    }
                }
            };

            updateTimer();
            var timerIv = setInterval(updateTimer, 1000);
        }
    @endif
});
</script>
@endsection
