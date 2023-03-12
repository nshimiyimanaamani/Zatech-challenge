<?php

namespace App\Http\Controllers;

use App\Models\Album;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): array
    {
        $user = $request->user();
        // total albums created by user 
        $total = Album::where('user_id', $user->id)->count();  
        $album = Album::where('user_id', $user->id)->get();

        

        return [
            'totalAlbums' => $total,
            'totalSongs' =>$album->sum(function ($a) {
                return $a->getTotalSongsAttribute();
            }),
    
        ];
    }
}