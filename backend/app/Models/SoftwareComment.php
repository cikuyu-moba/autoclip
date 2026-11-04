<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoftwareComment extends Model
{
    protected $fillable = [
        'software_id',
        'parent_id',
        'username',
        'type',
        'user_id',
        'content',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SoftwareComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SoftwareComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
