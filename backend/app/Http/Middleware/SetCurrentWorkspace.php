<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use App\Support\CurrentWorkspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentWorkspace
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $workspaceId = $request->header('X-Workspace-Id');

        if (! $workspaceId || ! ctype_digit((string) $workspaceId)) {
            return response()->json([
                'message' => 'A valid X-Workspace-Id header is required.',
            ], 400);
        }

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Only allow workspaces that the authenticated user belongs to.
        $workspace = $user->workspaces()
            ->where('workspaces.id', (int) $workspaceId)
            ->first();

        if (! $workspace) {
            return response()->json([
                'message' => 'Workspace not found or access denied.',
            ], 404);
        }

        $currentWorkspace = app(CurrentWorkspace::class);
        $currentWorkspace->set($workspace);

        try {
            return $next($request);
        } finally {
            $currentWorkspace->clear();
        }
    }
}