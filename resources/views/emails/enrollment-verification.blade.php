<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enrollment Email Verification</title>
</head>
@php
    $themeKey = strtolower((string) ($school->theme ?? 'blue'));
    $themeMap = [
        'blue' => ['accent' => '#2563eb', 'accent_h' => '#1d4ed8', 'accent_l' => '#eff6ff', 'accent_l2' => '#dbeafe', 'accent_text' => '#1e3a8a'],
        'green' => ['accent' => '#15803d', 'accent_h' => '#166534', 'accent_l' => '#ecfdf5', 'accent_l2' => '#dcfce7', 'accent_text' => '#14532d'],
        'indigo' => ['accent' => '#4f46e5', 'accent_h' => '#4338ca', 'accent_l' => '#eef2ff', 'accent_l2' => '#e0e7ff', 'accent_text' => '#3730a3'],
        'slate' => ['accent' => '#475569', 'accent_h' => '#334155', 'accent_l' => '#f1f5f9', 'accent_l2' => '#e2e8f0', 'accent_text' => '#1e293b'],
        'teal' => ['accent' => '#0f766e', 'accent_h' => '#115e59', 'accent_l' => '#f0fdfa', 'accent_l2' => '#ccfbf1', 'accent_text' => '#134e4a'],
        'amber' => ['accent' => '#d97706', 'accent_h' => '#b45309', 'accent_l' => '#fffbeb', 'accent_l2' => '#fde68a', 'accent_text' => '#92400e'],
        'rose' => ['accent' => '#e11d48', 'accent_h' => '#be123c', 'accent_l' => '#fff1f2', 'accent_l2' => '#fecdd3', 'accent_text' => '#9f1239'],
        'purple' => ['accent' => '#7c3aed', 'accent_h' => '#6d28d9', 'accent_l' => '#f5f3ff', 'accent_l2' => '#ddd6fe', 'accent_text' => '#5b21b6'],
        'emerald' => ['accent' => '#059669', 'accent_h' => '#047857', 'accent_l' => '#ecfdf5', 'accent_l2' => '#a7f3d0', 'accent_text' => '#065f46'],
        'sky' => ['accent' => '#0284c7', 'accent_h' => '#0369a1', 'accent_l' => '#e0f2fe', 'accent_l2' => '#bae6fd', 'accent_text' => '#075985'],
    ];
    $theme = $themeMap[$themeKey] ?? $themeMap['blue'];
@endphp
<body style="margin:0; padding:0; background:#f3f6fb; font-family:Inter,Segoe UI,Arial,sans-serif; color:#0f172a;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f3f6fb; padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:620px; background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;">
                    <tr>
                        <td style="padding:22px 24px; border-bottom:1px solid #e2e8f0; background:linear-gradient(135deg,{{ $theme['accent'] }},{{ $theme['accent_h'] }}); color:#ffffff;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td valign="middle" style="width:52px;">
                                        @if(!empty($school->logo_url))
                                            <img src="{{ $school->logo_url }}" alt="{{ $school->name }} logo" width="44" height="44" style="display:block; width:44px; height:44px; border-radius:10px; object-fit:cover; border:1px solid rgba(255,255,255,.35); background:#fff;">
                                        @else
                                            <div style="width:44px; height:44px; border-radius:10px; background:rgba(255,255,255,.15); color:#ffffff; font-size:20px; line-height:44px; text-align:center; font-weight:700;">
                                                {{ strtoupper(mb_substr($school->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td valign="middle" style="padding-left:12px;">
                                        <div style="font-size:12px; letter-spacing:.08em; text-transform:uppercase; opacity:.88; font-weight:700;">Enrollment Verification</div>
                                        <div style="font-size:18px; line-height:1.3; font-weight:800;">{{ $school->name }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 10px; font-size:15px;">Hello {{ $name }},</p>
                            <p style="margin:0 0 16px; font-size:14px; color:#334155; line-height:1.6;">
                                Use this one-time code to verify your email and continue your enrollment.
                            </p>

                            <div style="margin:0 0 16px; padding:16px; border:1px solid {{ $theme['accent_l2'] }}; border-radius:12px; background:{{ $theme['accent_l'] }}; text-align:center;">
                                <div style="font-size:11px; text-transform:uppercase; letter-spacing:.09em; color:{{ $theme['accent_h'] }}; font-weight:700; margin-bottom:8px;">Your Verification Code</div>
                                <div style="font-size:34px; line-height:1; letter-spacing:.18em; font-weight:800; color:{{ $theme['accent_text'] }}; font-family:'Courier New',Consolas,monospace;">
                                    {{ $code }}
                                </div>
                                <div style="margin-top:10px; font-size:12px; color:#334155;">
                                    Expires in {{ (int) $expiresInMinutes }} minutes
                                </div>
                            </div>

                            <p style="margin:0 0 16px; font-size:13px; color:#475569; line-height:1.55;">
                                You can also open the verification page here:
                                <a href="{{ $verificationUrl }}" style="color:{{ $theme['accent'] }}; text-decoration:none;">Verify email</a>
                            </p>

                            <p style="margin:0; font-size:12px; color:#64748b; line-height:1.6;">
                                If you did not request this code, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:14px 24px; border-top:1px solid #e2e8f0; background:#f8fafc; font-size:11px; color:#64748b;">
                            This is an automated message from {{ $school->name }} enrollment system.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
