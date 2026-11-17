<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $workspace = Workspace::create([
                'name' => $data['workspace_name'],
                'slug' => Str::slug($data['workspace_name'])
                    . '-' . Str::lower(Str::random(6)),
                'owner_id' => $user->id,
                'currency' => $data['currency'],
                'timezone' => $data['timezone'] ?? 'UTC',
            ]);

            $workspace->users()->attach($user->id, [
                'role' => 'owner',
            ]);

            $token = $user->createToken(
                'invoice-payment-chaser'
            )->plainTextToken;

            return [
                'user' => $user,
                'workspace' => $workspace,
                'token' => $token,
            ];
        });
    }
}