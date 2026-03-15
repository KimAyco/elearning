<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'auto_approve_payments',
        'updated_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'auto_approve_payments' => 'bool',
        ];
    }
}

