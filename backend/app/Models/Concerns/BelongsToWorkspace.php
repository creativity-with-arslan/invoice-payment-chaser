<?php

namespace App\Models\Concerns;

use App\Models\Scopes\WorkspaceScope;
use App\Support\CurrentWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToWorkspace
{
    protected static function bootBelongsToWorkspace(): void
    {
        static::addGlobalScope(new WorkspaceScope());

        static::creating(function (Model $model): void {
            $workspaceId = app(CurrentWorkspace::class)->id();

            if ($workspaceId === null) {
                throw new \RuntimeException(
                    'Cannot create this record without an active workspace.'
                );
            }

            $model->setAttribute('workspace_id', $workspaceId);
        });
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }
}