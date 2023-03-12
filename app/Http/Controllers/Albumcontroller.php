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
    // returns all Albums
    public function index(Request $request)
    {
        //
        $user = $request->user();
        // get all the albums created by the user
        return AlbumResource::collection(
            Album::where('user_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->paginate(10)
        );
    }
    //Stores Album
    public function store(Request $request): AlbumResource
    {
        // validate the request
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'release_date' => 'required',
        ]);

        // store the image
        $path = "";
        if ($request->has('image')) {
            $path = $this->saveImage($request->image);
        }

        // store the data
        $album = $request->user()->albums()->create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'image' => $path,
            'release_date' => $validatedData['release_date'],
        ]);

        // return the response
        return new AlbumResource($album);
    }
    // returns an Album with a given id
    public function show(Request $request, Album $album): AlbumResource
    {
        $user = $request->user();
        if ($user->id !== $album->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        return new AlbumResource($album);
    }

    //updates the album given
    public function update(Request $request, Album $album): AlbumResource
    {
        // validate the request
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'release_date' => 'required',
        ]);
        // check if the user is authorized to update the album
        if ($request->user()->id !== $album->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        // check if image is sent
        if ($request->hasFile('image')) {
            // delete the old image
            if ($album->image) {
                Storage::delete($album->image);
            }
            // store the new image
            $path = $request->file('image')->store('public/albums');
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
    //saves the image and checks if it's valid
    private function saveImage(string $image): string
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
    
        // Set the directory and filename for the image
        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
    
        // Create the directory if it doesn't exist
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        dd($absolutePath);
        // Save the image to the server
        file_put_contents($relativePath, $image);
    
        // Return the path to the image
        return $relativePath;
    }
    // deletes Album
    public function destroy(Album $album, Request $request)
    {
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
        $user = $request->user();

        // Get all albums and eager load the songs relationship
        $albums = Album::with('songs')
            ->orderBy('created_at', 'DESC')
            ->paginate(10);


        // Return the response
        return AlbumResource::collection($albums);
    }
}