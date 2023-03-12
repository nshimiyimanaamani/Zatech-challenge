<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Album;
use Illuminate\Support\Facades\URL;

class AlbumControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_albums()
    {
        // Create a user and multiple albums
        $user = User::factory()->create();
        $albums = Album::factory()->count(3)->create(['user_id' => $user->id]);
    
        // Make a GET request to the index method of the AlbumController
        $response = $this->actingAs($user)->get('/api/albums');
    
        // Assert that the response has a 200 OK status code
        $response->assertStatus(200);
    
        // Assert that the response contains all the albums' data
        $response->assertJsonCount(3);
        foreach ($albums as $album) {
            
            $response->assertJsonFragment([
                'title' => $album->title,
                'description' => $album->description,
                'image_url' => $album->image ? URL::to($album->image) : null,
               
            ]);
        }
    }
    



}