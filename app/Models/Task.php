<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Task extends Model
{
    use HasTranslations, SoftDeletes;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'project_id',
        'category_id',
        'project_budget_id',
        'name',
        'description',
        'contract_amount',
        'final_total',
        'rest_total',
        'paid_total',
        'start_date',
        'end_date',
        'status',
        'progress_percentage',
    ];

    protected static function booted()
    {
        static::saving(function ($task) {
            // For tasks, final_total can be set to contract_amount for borongan workers
            if ($task->contract_amount > 0 && empty($task->final_total)) {
                $task->final_total = $task->contract_amount;
            }
            $task->rest_total = $task->final_total - $task->paid_total;
        });
    }

    protected function casts(): array
    {
        return [
            'contract_amount' => 'decimal:2',
            'final_total' => 'decimal:2',
            'rest_total' => 'decimal:2',
            'paid_total' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function projectBudget(): BelongsTo
    {
        return $this->belongsTo(ProjectBudget::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'task_workers')
            ->withPivot('paid')
            ->withTimestamps();
    }

    public function purchaseTasks(): HasMany
    {
        return $this->hasMany(PurchaseTask::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }
}
