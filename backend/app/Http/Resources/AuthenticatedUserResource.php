<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthenticatedUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,

            'workspaces' => $this->whenLoaded(
                'workspaces',
                fn () => $this->workspaces->map(fn ($workspace) => [
                    'id' => $workspace->id,
                    'name' => $workspace->name,
                    'slug' => $workspace->slug,
                    'currency' => $workspace->currency,
                    'role' => $workspace->pivot->role,
                ])->values()
            ),
        ];
    }
}