<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_me_endpoint_returns_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/users/me');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User retrieved successfully',
                'status' => false,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }
}