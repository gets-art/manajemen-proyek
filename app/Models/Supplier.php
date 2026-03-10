<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Supplier extends Model
{
    use HasTranslations, SoftDeletes;

    public array $translatable = ['name'];

    protected $fillable = [
        'name',
        'phone',
        'address',
        'image',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function purchaseTasks(): HasMany
    {
        return $this->hasMany(PurchaseTask::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }
}
