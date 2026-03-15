<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingGroupItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_group_id',
        'billing_rule_id',
        'sort_order',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(BillingGroup::class, 'billing_group_id');
    }

    public function billingRule(): BelongsTo
    {
        return $this->belongsTo(BillingRule::class, 'billing_rule_id');
    }
}
