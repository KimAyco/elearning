<?php

namespace App\Support;

final class PlatformEnums
{
    public const SCHOOL_STATUS_ACTIVE = 'active';
    public const SCHOOL_STATUS_SUSPENDED = 'suspended';

    public const ENROLLMENT_STATUSES = [
        'selected',
        'validated',
        'billing_pending',
        'payment_verified',
        'registrar_confirmed',
        'enrolled',
        'dropped',
        'cancelled',
        'rejected',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid',
        'partial',
        'paid_unverified',
        'verified',
        'waived',
        'void',
    ];

    public const GRADE_STATUSES = [
        'draft',
        'submitted',
        'dean_approved',
        'dean_rejected',
        'registrar_finalized',
        'released',
    ];

    private function __construct()
    {
    }
}

