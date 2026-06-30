<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTerm extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'percentage',
        'amount',
        'target_progress_percentage',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
            'amount' => 'decimal:2',
            'target_progress_percentage' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::saving(function ($term) {
            if ($term->project && $term->percentage) {
                $term->amount = ($term->percentage / 100) * $term->project->final_total;
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
