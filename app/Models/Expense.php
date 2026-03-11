<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'value',
        'date',
        'added_by',
        'last_edit_by',
        'expense_category_id',
        'project_id',
        'payment_method_id',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'double',
            'date' => 'date',
        ];
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function lastEditBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edit_by');
    }
}
