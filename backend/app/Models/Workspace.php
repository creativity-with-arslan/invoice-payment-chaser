<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'owner_id',
        'currency',
        'timezone',
        'reminder_hour',
        'logo_path',
        'brand_color',
        'reply_to_email',
        'stripe_account_id',
        'plan',
    ];

    protected function casts(): array
    {
        return [
            'reminder_hour' => 'integer',
        ];
    }

    /**
     * The user who owns this workspace.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * All users who belong to this workspace.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }
}
