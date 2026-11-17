<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthenticatedUserResource;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $baseSlug = Str::slug($data['workspace_name']);
            $slug = $baseSlug;
            $counter = 1;

            while (Workspace::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $workspace = Workspace::create([
                'name' => $data['workspace_name'],
                'slug' => $slug,
                'owner_id' => $user->id,
                'currency' => $data['currency'],
                'timezone' => $data['timezone'] ?? 'UTC',
                'plan' => 'free',
            ]);

            $workspace->users()->attach($user->id, [
                'role' => 'owner',
            ]);

            return $user;
        });

        $token = $user->createToken('auth-token')->plainTextToken;

        $user->load('workspaces');

        return response()->json([
            'message' => 'Registration successful.',
            'token' => $token,
            'user' => new AuthenticatedUserResource($user),
        ], 201);
    }
}