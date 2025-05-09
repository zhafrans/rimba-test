<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_user_list()
    {
        // Arrange
        User::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/users');

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'items',
                        'meta' => [
                            'current_page',
                            'last_page',
                            'per_page',
                            'total'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_can_create_new_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'age' => 25
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'User created successfully'
                ]);
    }

    /** @test */
    public function it_can_display_user_details()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'code'
                    ]
                ]);
    }

    /** @test */
    public function it_can_update_user()
    {
        $user = User::factory()->create();
        
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'age' => 30
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]);
    }

    /** @test */
    public function it_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
