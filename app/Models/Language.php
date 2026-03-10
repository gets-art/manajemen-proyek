<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'symbol',
        'direction',
        'active',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }
}
