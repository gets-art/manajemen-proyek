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
        'name',
        'description',
        'final_total',
        'rest_total',
        'paid_total',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'final_total' => 'double',
            'rest_total' => 'double',
            'paid_total' => 'double',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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
}
