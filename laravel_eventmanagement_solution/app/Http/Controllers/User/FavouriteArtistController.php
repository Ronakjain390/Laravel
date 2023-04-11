<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artists;
use App\Models\FavouriteArtist;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteArtistController extends Controller
{
    public function index($id)
    {
        if (Auth::user()) {
            $wish = FavouriteArtist::all()->where('artist_id', $id)->where('user_id', Auth::user()->id);
            if (count($wish) == null) {
                $favourite = new FavouriteArtist();
                $favourite->artist_id = $id;
                $favourite->user_id = (Auth::user()->id);
                $favourite->save();
                return redirect()->back()->with('success', "You added in wishlist.");
            } else {
                $del = FavouriteArtist::where('artist_id',$id)->where('user_id',Auth::user()->id)->delete();
                return redirect()->back()->with('error', "Removed from WishList.");
            }
        } else {
            return view('auth/login');
        }
    }

    public function ViewFavouriteArtist()
    {
        $favourite = FavouriteArtist::where('user_id', Auth::user()->id)->pluck('artist_id');
        $artist = Artist::select("*")->whereIn('id', $favourite)->get();
        // gettype(print_r($artist)); die;
        return view('User/Pages/FavouriteArtist/Show',compact('artist'));
    }
}
