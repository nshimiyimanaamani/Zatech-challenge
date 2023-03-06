<?php

namespace App\Http\Controllers;

use App\Http\Resources\SongResource;
use App\Models\Songs;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         // validate the request
         $request->validate([
            'title' => 'required',
            'length' => 'required',
            'gerne' => 'required|in:' . implode(',', Songs::GERNES),
            'albums' => 'required|exists:albums,id',
            
        ]);
        // store the image
      

        // store the data
        $song = new Songs();
        $song->title = $request->title;
        $song->length = $request->length;
        $song->gerne = $request->gerne;
        $song->album_id = $request->albums;
        $song->save();
        // return the response
        return new SongResource($song);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {   
        //check if the user is authorized to view the song
        $song = Songs::find($id);
        if ($song->album->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to view this song'
            ], 403);
        }
        return new SongResource($song);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // validate the request
        $request->validate([
            'title' => 'required',
            'length' => 'required',
            'gerne' => 'required|in:' . implode(',', Songs::GERNES),
            'albums' => 'required|exists:albums,id',
            
        ]);

        // find the song
        $song = Songs::find($id);
        // return error when not found
        if (!$song) {
            return response()->json([
                'message' => 'Song not found'
            ], 404);
        }
        // check if the user is authorized to update the song
        if ($song->album->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to update this song'
            ], 403);
        }
        // update the song
        $song->title = $request->title;
        $song->length = $request->length;
        $song->gerne = $request->gerne;
        $song->album_id = $request->albums;
        $song->save();
        // return the response
        return new SongResource($song);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        // check if the user is authorized to delete the song
        $song = Songs::find($id);
        if ($song->album->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this song'
            ], 403);
        }
        $song->delete();

        return response('', 204);

    }

    // list all songs
    public function listSongs(Request $request)
    {
        // check if the user is authorized to view the songs
        $songs = Songs::orderBy('created_at', 'DESC')->paginate(10);
            // return the response
            return SongResource::collection($songs);
        }


    }

