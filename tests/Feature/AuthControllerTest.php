<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_register_a_new_user(): void
    {
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function it_can_login_an_existing_user(): void
    {
        $password = 'password';
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $data = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'token'
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_cannot_login_with_invalid_credentials(): void
    {
        $data = [
            'email' => $this->faker->safeEmail,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

  
}