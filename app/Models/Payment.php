<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_method_id',
        'paid',
        'paymentable_type',
        'paymentable_id',
        'payment_code',
    ];

    protected function casts(): array
    {
        return [
            'paid' => 'double',
        ];
    }

    public function paymentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
