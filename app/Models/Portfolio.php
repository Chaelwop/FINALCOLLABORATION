<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id',
    'user_id',
    'full_name',
    'email',
    'template',
    'is_public',
    'data',
])]
class Portfolio extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The templates a portfolio can be rendered with.
     */
    public const TEMPLATES = ['simple', 'modern', 'creative'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  Builder<Portfolio>  $query
     */
    public function scopeOwnedBy(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * @param  Builder<Portfolio>  $query
     */
    public function scopePublic(Builder $query): void
    {
        $query->where('is_public', true);
    }

    /**
     * Normalise the stored payload into a predictable shape.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = $this->data ?? [];

        return [
            'id' => $this->id,
            'fullName' => $this->full_name,
            'email' => $this->email,
            'template' => $this->template,
            'isPublic' => (bool) $this->is_public,
            'jobTitle' => $data['jobTitle'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'profilePic' => $data['profilePic'] ?? '',
            'aboutMe' => $data['aboutMe'] ?? '',
            'eduSchool' => $data['eduSchool'] ?? '',
            'eduDegree' => $data['eduDegree'] ?? '',
            'eduYear' => $data['eduYear'] ?? '',
            'skills' => $data['skills'] ?? [],
            'projects' => $data['projects'] ?? [],
            'experience' => $data['experience'] ?? [],
            'socialGithub' => $data['socialGithub'] ?? '',
            'socialLinkedin' => $data['socialLinkedin'] ?? '',
            'socialWebsite' => $data['socialWebsite'] ?? '',
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
