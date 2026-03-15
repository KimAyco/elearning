<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billing';

    protected $appends = [
        'scope_type',
        'scope_id',
    ];

    protected $fillable = [
        'school_id',
        'semester_id',
        'student_user_id',
        'enrollment_id',
        'billing_rule_id',
        'charge_type',
        'description',
        'amount_due',
        'amount_paid',
        'payment_status',
        'clearance_status',
        'generated_by_finance_user_id',
        'verified_by_finance_user_id',
        'cleared_by_finance_user_id',
        'approved_by_registrar_user_id',
        'due_date',
        'verified_at',
        'cleared_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_due' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'due_date' => 'date',
            'verified_at' => 'datetime',
            'cleared_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getScopeTypeAttribute(): string
    {
        if ($this->relationLoaded('rule') && $this->rule !== null) {
            return (string) $this->rule->scope_type;
        }

        if ($this->billing_rule_id !== null) {
            return 'rule';
        }

        return 'student';
    }

    public function getScopeIdAttribute(): ?int
    {
        if ($this->relationLoaded('rule') && $this->rule !== null) {
            return $this->rule->scope_id;
        }

        if ($this->billing_rule_id !== null) {
            return (int) $this->billing_rule_id;
        }

        return (int) $this->student_user_id;
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(BillingRule::class, 'billing_rule_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function clearedByFinance(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleared_by_finance_user_id');
    }

    public function approvedByRegistrar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_registrar_user_id');
    }
}
