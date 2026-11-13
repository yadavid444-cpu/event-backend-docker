<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'location', 'start_at', 'end_at',
        'capacity', 'image', 'is_published', 'created_by',
    ];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'capacity' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function approvedRegistrations(): HasMany
    {
        return $this->registrations()->where('status', EventRegistration::APPROVED);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function approvedCount(): int
    {
        return $this->approved_registrations_count ?? $this->approvedRegistrations()->count();
    }

    public function isFull(): bool
    {
        return $this->capacity !== null && $this->approvedCount() >= $this->capacity;
    }

    public function hasStarted(): bool
    {
        return $this->start_at->isPast();
    }
}
