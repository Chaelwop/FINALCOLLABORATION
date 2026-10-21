<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'purpose',
    'code_hash',
    'attempts',
    'expires_at',
    'consumed_at',
])]
#[Hidden(['code_hash'])]
class TwoFactorCode extends Model
{
    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
        ];
    }

    /**
     * Determine whether the code is still waiting to be used.
     */
    public function isUsable(): bool
    {
        return $this->consumed_at === null && $this->expires_at->isFuture();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
