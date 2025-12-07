<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_requires_email_when_registering()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_register(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'authorization' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $userData = [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login(): void
    {
        $password = 'password';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->postJson('/api/auth/login', $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'authorization' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                ],
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => 'wrong-password',
        ];

        $response = $this->postJson('/api/auth/login', $credentials);

        $response->assertStatus(401) // Unauthorized
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }
}
