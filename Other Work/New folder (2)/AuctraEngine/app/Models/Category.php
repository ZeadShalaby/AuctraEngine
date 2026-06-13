<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

        protected $appends = ['name'];

        protected $hidden = [
            'name_ar',
            'name_en',
            'created_at',
            'updated_at',
        ];

        public function getNameAttribute(): string
        {
            return app()->getLocale() === 'en' ? $this->name_en : $this->name_ar;
        }
}
