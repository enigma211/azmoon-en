<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'terms_content',
        'about_content',
        'site_name',
        'logo',
        'favicon',
        'seo_title',
        'site_description',
        'seo_keywords',
        'hero_title',
        'hero_description',
    ];

    protected $casts = [
        'value' => 'string',
    ];
}
