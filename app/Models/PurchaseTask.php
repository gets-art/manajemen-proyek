<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseTask extends Model
{
    protected $fillable = [
        'supplier_id',
        'task_id',
        'product_id',
        'quantity',
        'unit_price',
        'total',
        'discount',
        'final_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'double',
            'total' => 'double',
            'discount' => 'double',
            'final_total' => 'double',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
