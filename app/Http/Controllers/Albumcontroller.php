<?php

namespace App\Http\Controllers;

use App\Http\Resources\AlbumResource;
use App\Models\Album;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str; 
//import all the classes


class Albumcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $user = $request->user();
        // get all the albums created by the user
        return AlbumResource::collection(Album::where('user_id', $user->id)->orderBy('created_at', 'DESC')->paginate(10));

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
            'description' => 'required',
            'release_date' => 'required',
        ]);
        // store the image
        $path = "";
        if (isset($request->image)) {
            $relativePath  = $this->saveImage($request->image);
           $path  = $relativePath;
        }

        // store the data
        $album = new Album();
        $album->title = $request->title;
        $album->description = $request->description;
        $album->image = $path;
        $album->release_date = $request->release_date;
        $album->user_id = $request->user()->id;
        $album->save();
        // return the response
        return new AlbumResource($album);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Album $album)
    {
        $user = $request->user();
        if ($user->id !== $album->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        return new AlbumResource($album);
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
            'description' => 'required',
            'release_date' => 'required',
        ]);
        // find the album
        $album = Album::find($id);
        // check if the user is authorized to update the album
        if ($request->user()->id !== $album->user_id) {
            return abort(403, 'Unauthorized action.');
        }
        // check if image is sent
        if (isset($request->image)) {
            // delete the old image
            if ($album->image) {
                Storage::delete($album->image);
            }
            // store the new image
            $relativePath  = $this->saveImage($request->image);
            $path  = $relativePath;
            // update the image
            $album->image = $path;
        }

        // update the data
        $album->title = $request->title;
        $album->description = $request->description;
       
        $album->release_date = $request->release_date;
        $album->save();
        // return the response
        return new AlbumResource($album);
    }


    private function saveImage($image)
    {
        // Check if image is valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            // Take out the base64 encoded text without mime type
            $image = substr($image, strpos($image, ',') + 1);
            // Get file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // Check if file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }

        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Album $album, Request $request)
    {
        $user = $request->user();
        if ($user->id !== $album->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        $album->delete();

        // If there is an old image, delete it
        if ($album->image) {
            $absolutePath = public_path($album->image);
            File::delete($absolutePath);
        }

        return response('', 204);
    }


    //list all albums
    public function listAlbums(Request $request)
    {
        // check if the user is authorized to view the songs
        $albums = Album::orderBy('created_at', 'DESC')->paginate(10);
        $albums->load('songs');
            // return the response
            return AlbumResource::collection($albums);
        }


    }


