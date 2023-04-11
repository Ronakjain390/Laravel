<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Expertize;
use App\Models\FavouriteArtist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontArtistController extends Controller
{
    public function index()
    {
        $artist = Artist::where('type' , '=', NULL)->get();
        $user_id = isset(Auth::user()->id)? Auth::user()->id : '';
        $wish = [];
        if($user_id){
        $wish = FavouriteArtist::where('user_id', $user_id)->get();
        }

        // print_r($wish); die;

        return view('User/Pages/Artist/Index', compact('artist', 'wish'));
    }

    public function show($id)
    {
        $artist = Artist::where('id', $id)->get();
        $gallary_images = Artist::where('id', $id)->first();
        // dd($artist);
        //  echo json_decode($artist->gallary_images); die;
        $ex = DB::table('artist_expertize')->where('artist_id', $id)->pluck('expertize_id')->toArray();

        $expertize = Expertize::whereIn('id', $ex)->get();

        return view('User/Pages/Artist/Show', compact('artist', 'expertize', 'gallary_images'));
    }
}
