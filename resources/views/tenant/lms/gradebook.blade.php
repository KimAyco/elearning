@extends('layouts.app')

@section('title', 'Gradebook - ' . $subject->code)

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'class', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <div class="page-body">
            <div class="lms-topbar">
                <div class="lms-topbar-left">
                    <a href="{{ url('/tenant/lms/classes/'.$classGroup->id.'/'.$subject->id) }}" class="lms-topbar-menu">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                    <h1 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">Gradebook — {{ $subject->code }}</h1>
                </div>
            </div>
            <div class="mdl-layout">
                <main class="mdl-main">
                    <div class="mdl-block">
                        <div class="mdl-block-title" style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Quiz Performance Summary</span>
                            <span class="mdl-tag">{{ $classGroup->name }} — {{ $subject->title }}</span>
                        </div>
                        <div class="lms-table-container">
                            <table class="lms-table gradebook-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 200px; text-align: left;">Student Name</th>
                                        @foreach($quizzes as $quiz)
                                            <th style="text-align: center;">
                                                <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; margin-bottom: 4px;">{{ $quiz->title }}</div>
                                                <div style="font-size: 0.85rem; color: #0f172a;">/{{ $quiz->questions()->sum('points') }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                        @if($student)
                                        <tr>
                                            <td style="text-align: left;">
                                                <div style="font-weight: 700; color: #0f172a;">{{ $student->full_name ?? 'Unknown' }}</div>
                                                <div style="font-size: 0.75rem; color: #94a3b8;">{{ $student->email ?? '' }}</div>
                                            </td>
                                            @foreach($quizzes as $quiz)
                                                @php
                                                    $studentAttempt = $attempts->get($student->id . '_' . $quiz->id)?->first();
                                                @endphp
                                                <td style="text-align: center;">
                                                    @if($studentAttempt)
                                                        <div style="font-size: 1rem; font-weight: 800; color: {{ $studentAttempt->status === 'graded' ? '#10B981' : '#f59e0b' }};">
                                                            {{ number_format($studentAttempt->score, 1) }}
                                                        </div>
                                                        <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; margin-top: 2px;">
                                                            {{ $studentAttempt->status }}
                                                        </div>
                                                    @else
                                                        <div style="font-size: 1rem; font-weight: 400; color: #e2e8f0;">—</div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($quizzes) + 1 }}" style="padding: 40px; text-align: center; color: #94a3b8;">
                                                No students enrolled in this course yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>

<style>
.tenant-ui-mock .page-body .lms-topbar { position: relative; top: auto; margin: 0 0 20px 0; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.lms-topbar { background: #fff; height: 60px; display: flex; align-items: center; padding: 0 24px; }
.lms-topbar-menu { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: #f1f5f9; color: #64748b; margin-right: 16px; }
.mdl-block { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
.mdl-block-title { padding: 12px 24px; background: #fafbff; border-bottom: 1px solid #eef2f7; font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }

.lms-table-container { width: 100%; overflow-x: auto; }
.lms-table { width: 100%; border-collapse: collapse; }
.lms-table th, .lms-table td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.lms-table thead th { background: #fafbff; }

.gradebook-table tr:hover td { background: #f8fafc; }

.mdl-tag { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 8px; background: #f1f5f9; color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
</style>
@endsection
