<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'name',
        'type',
        'file_path',
        'documentable_id',
        'documentable_type',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
