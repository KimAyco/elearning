<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_user_id',
        'balance',
        'last_transaction_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'last_transaction_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}
