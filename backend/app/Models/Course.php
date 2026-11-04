<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'materials'];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(TestScore::class);
    }
}
