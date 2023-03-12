<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Songs;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SongControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function testIndexReturnsSongsBelongingToUser()
    {
        $user = $this->signIn();

        // create an album belonging to the user
        $album = Album::factory()->create([
            'user_id' => $user->id,
        ]);

        // create 3 songs for the album
        $songs = Songs::factory()->count(3)->create([
            'album_id' => $album->id,
        ]);

        // call the API endpoint to get songs belonging to the user
        $response = $this->getJson('/api/song')->assertOk();

        // check that all 3 songs belonging to the user are returned
        $response->assertJsonCount(3);
        $response->assertJsonFragment([
            'title' => $songs[0]->title,
            'length' => $songs[0]->length,
            'gerne' => $songs[0]->gerne,
        ]);
    }
    public function testCreateSong()
    {
        $user = $this->signIn();

        // create an album belonging to the user
        $album = Album::factory()->create([
            'user_id' => $user->id,
        ]);

        // select a random genre from the list of genres defined in the Songs model
        $genre = Songs::GENRES[array_rand(Songs::GENRES)];

        $songData = [
            'title' => $this->faker->sentence,
            'length' => $this->faker->time('i:s'),
            'gerne' => $genre,
            'album_id' => $album->id,
        ];

        // call the API endpoint to create a new song
        $response = $this->postJson('/api/song', $songData)->assertCreated();

        // check that the song has been created
        $this->assertDatabaseHas('songs', $songData);

        // check that the API returns the created song
        $response->assertJsonFragment($songData);
    }


    public function testUpdateSong()
    {
        $user = $this->signIn();

        // create an album belonging to the user
        $album = Album::factory()->create([
            'user_id' => $user->id,
        ]);

        // create a song belonging to the album
        $song = Songs::factory()->create([
            'album_id' => $album->id,
        ]);

        // select a random genre from the list of genres defined in the Songs model
        $genre = Songs::GENRES[array_rand(Songs::GENRES)];

        $updatedData = [
            'title' => $this->faker->sentence,
            'length' => $this->faker->time('i:s'),
            'gerne' => $genre,
            'album_id' => $album->id,
        ];

        // call the API endpoint to update the song
        $response = $this->putJson('/api/song/' . $song->id, $updatedData)->assertOk();

        // check that the song has been updated
        $this->assertDatabaseHas('songs', $updatedData);

        // check that the API returns the updated song
        $response->assertJsonFragment($updatedData);
    }

    public function testDeleteSong()
    {
        $user = $this->signIn();

        // create an album belonging to the user
        $album = Album::factory()->create([
            'user_id' => $user->id,
        ]);

        // create a song belonging to the album
        $song = Songs::factory()->create([
            'album_id' => $album->id,
        ]);

        // call the API endpoint to delete the song
        $response = $this->deleteJson('/api/song/' . $song->id)->assertNoContent();

        // check that the song has been deleted from the database
        $this->assertDatabaseMissing('songs', [
            'id' => $song->id,
        ]);
    }




    // sign in method
    protected function signIn()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }
}