<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Settlement extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = ['amount' => 'decimal:2', 'confirmed_at' => 'datetime', 'metadata' => 'array'];

    public function paymentIntent(): BelongsTo
    {
        return $this->belongsTo(PaymentIntent::class);
    }

    public function reconciliation(): HasOne
    {
        return $this->hasOne(Reconciliation::class);
    }
}
