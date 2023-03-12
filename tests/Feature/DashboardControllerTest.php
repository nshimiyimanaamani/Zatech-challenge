<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Songs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_total_albums_and_songs_created_by_user()
    {
        // Create a user
        $user = User::factory()->create();

        // Create two albums and one song for the user
        $album1 = Album::factory()->create(['user_id' => $user->id]);
        $album2 = Album::factory()->create(['user_id' => $user->id]);
        Songs::factory()->create(['album_id' => $album1->id]);
        Songs::factory()->create(['album_id' => $album2->id]);
        Songs::factory()->create(['album_id' => $album2->id]);

        // Make a GET request to the index method of the DashboardController
        $response = $this->actingAs($user)->get('/api/dashboard');

        // Assert that the response has a 200 OK status code
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the total number of albums and songs
        $response->assertExactJson([
            'totalAlbums' => 2,
            'totalSongs' => 3,
        ]);
    }
}

