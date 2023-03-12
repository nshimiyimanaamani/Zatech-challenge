<?php

namespace App\Http\Controllers;

use App\Http\Resources\SongResource;
use App\Models\Songs;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function index(Request $request)
    {
        // get songs for the album that belongs to the user
        $user = $request->user();
        $songs = Songs::whereHas('album', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('created_at', 'DESC')->paginate(10);

        // return the response
        return SongResource::collection($songs);
    }

    public function store(Request $request): SongResource
    {
        // validate the request
        $request->validate([
            'title' => 'required',
            'length' => 'required',
            'gerne' => 'required|in:' . implode(',', Songs::GENRES),
            'album_id' => 'required|exists:albums,id',
        ]);

        // store the data
        $song = new Songs();
        $song->title = $request->title;
        $song->length = $request->length;
        $song->gerne = $request->gerne;
        $song->album()->associate($request->album_id);
        $song->save();

        // return the response
        return new SongResource($song);
    }

    public function show(Request $request, Songs $song): SongResource
    {
        // Check if the user is authorized to view the song
        if ($song->album->user_id != $request->user()->id) {
            // Return a 403 Forbidden error if the user is not authorized
            abort(403, 'You are not authorized to view this song');
        }

        // If the user is authorized, return the song as a JSON resource
        return new SongResource($song);
    }


    public function update(Request $request, Songs $song)
    {
        // validate the request
        $request->validate([
            'title' => 'required',
            'length' => 'required',
            'gerne' => 'required|in:' . implode(',', Songs::GENRES),
            'album_id' => 'required|exists:albums,id',

        ]);

        // check if the user is authorized to update the song
        if ($song->album->user_id != $request->user()->id) {
            abort(403, 'You are not authorized to update this song');
        }

        // update the song
        $song->title = $request->title;
        $song->length = $request->length;
        $song->gerne = $request->gerne;
        $song->album()->associate($request->album_id);
      
        $song->save();

        // return the response
        return new SongResource($song);
    }

    public function destroy(Request $request, Songs $song)
    {
        // Check if the user is authorized to delete the song
        if ($song->album->user_id != $request->user()->id) {
            abort(403, 'You are not authorized to delete this song');
        }

        $song->delete();

        return response('', 204);
    }
    // list all songs
    public function listSongs(Request $request)
    {
        // Fetch all songs with pagination
        $songs = Songs::orderBy('created_at', 'DESC')->paginate(10);

        // Return a JSON resource collection of songs
        return SongResource::collection($songs);
    }
}
