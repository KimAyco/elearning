@extends('layouts.app')

@section('title', $quiz->title . ' - Results')

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
        </div>

        <div class="page-body" style="max-width: 600px; margin: 0 auto; padding: 40px 20px; text-align: center;">
            <div class="mdl-block" style="padding: 40px;">
                <div style="width: 80px; height: 80px; background: #f0fdf4; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 12px;">Quiz Submitted!</h2>
                <p style="color: #64748b; margin-bottom: 32px;">Your answers have been recorded successfully. @if($attempt->status === 'submitted') Some questions require manual grading by your teacher. @endif</p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 32px;">
                    <div style="background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Score</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ number_format($attempt->score, 1) }} / {{ $attempt->max_score }}</div>
                    </div>
                    <div style="background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Status</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: {{ $attempt->status === 'graded' ? '#10B981' : '#f59e0b' }}; text-transform: capitalize;">{{ $attempt->status }}</div>
                    </div>
                </div>

                <a href="{{ url('/tenant/classes/'.$quiz->class_group_id.'/'.$quiz->subject_id) }}" class="lms-btn-primary" style="display: inline-block; padding: 14px 40px; text-decoration: none;">
                    Back to Class
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.lms-dashboard-shell .main-content { background: #f8fafc; min-height: 100vh; }
.lms-topbar { background: #fff; height: 60px; display: flex; align-items: center; padding: 0 24px; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 100; }
.lms-topbar-menu { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: #f1f5f9; color: #64748b; margin-right: 16px; }
.mdl-block { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
.lms-btn-primary { background: #10B981; border: none; border-radius: 12px; color: #fff; font-weight: 700; cursor: pointer; transition: transform 0.2s; }
.lms-btn-primary:hover { transform: translateY(-2px); filter: brightness(1.05); }
</style>
@endsection
