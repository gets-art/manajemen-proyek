<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'client_id',
        'category_id',
        'start_date',
        'end_date',
        'status',
        'final_total',
        'paid_total',
        'rest_total',
        'observation',
        'image',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'final_total' => 'double',
            'paid_total' => 'double',
            'rest_total' => 'double',
            'observation' => 'double',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
