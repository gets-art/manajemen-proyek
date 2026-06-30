<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'project_id',
        'type',
        'quantity',
        'date',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function booted()
    {
        static::created(function ($transaction) {
            $product = $transaction->product;
            if ($transaction->type === 'In') {
                $product->stock += $transaction->quantity;
            } else {
                $product->stock -= $transaction->quantity;
            }
            $product->save();
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
