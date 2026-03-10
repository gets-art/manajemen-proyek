<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{
    use HasTranslations, SoftDeletes;

    public array $translatable = ['app_name'];

    protected $fillable = [
        'app_name',
        'contact_email',
        'contact_phone',
        'whatsapp',
        'facebook',
        'instagram',
        'image',
        'fav_ico',
        'default_lang',
    ];
}
