<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_create_new_user()
    {
        // Arrange
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'age' => 25,
            'code' => strtoupper(Str::random(8))
        ];

        // Act
        $user = User::create($userData);

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['code'], $user->code);
        $this->assertNotNull($user->code);
    }

    /** @test */
    public function it_can_retrieve_user_by_code()
    {
        // Arrange
        $user = User::factory()->create([
            'code' => strtoupper(Str::random(8))
        ]);

        // Act
        $foundUser = User::where('code', $user->code)->first();

        // Assert
        $this->assertNotNull($foundUser);
        $this->assertEquals($user->code, $foundUser->code);
    }

    /** @test */
    public function it_can_update_user()
    {
        // Arrange
        $user = User::factory()->create([
            'code' => strtoupper(Str::random(8))
        ]);
        
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'age' => 30
        ];

        // Act
        $user->update($updateData);

        // Assert
        $this->assertEquals($updateData['name'], $user->name);
        $this->assertEquals($updateData['email'], $user->email);
        $this->assertEquals($updateData['age'], $user->age);
    }

    /** @test */
    public function it_can_delete_user()
    {
        // Arrange
        $user = User::factory()->create([
            'code' => strtoupper(Str::random(8))
        ]);

        // Act
        $user->delete();

        // Assert
        $this->assertDatabaseMissing('users', [
            'code' => $user->code
        ]);
    }
}
