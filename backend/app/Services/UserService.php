<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct() {}

    public function create(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $plainPassword = 'Password@1234';

            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make($plainPassword),
                'email_verified_at' => now(), // admin-created users are pre-verified
            ]);

            // Assign role via Spatie
            $user->assignRole($data['role']);

            return [
                'user'     => $user->fresh(),
                'password' => $plainPassword, // return plain for email notification
            ];
        });
    }
}