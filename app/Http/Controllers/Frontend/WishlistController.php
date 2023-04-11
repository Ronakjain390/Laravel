<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use App\Models\PropertyManager;

class WishlistController extends Controller
{
    public function index($id)
    {
        if (Auth::user()) {
            $wish = Wishlist::all()->where('propertyId', $id)->where('userId', Auth::user()->id);
            if (count($wish) == null) {
                $wishlist = new Wishlist();
                $wishlist->propertyId = $id;
                $wishlist->userId = Auth::user()->id;
                $wishlist->save();
                return redirect()->route('home')->with('success', "You added in wishlist.");
            } else {
                return redirect()->route('home')->with('success', "Already added in WishList.");
            }
        }
        else {
            return view('auth/userlogin');
        }
    }

    public function show()
    {
        $wishlist = Wishlist::where('userId', Auth::user()->id)->pluck('propertyId')->toarray();
        $property_obj = PropertyManager::select("*")->whereIn('id', $wishlist)->get();

        return view('user/pages/wishlist/index')->with('property', $property_obj);
    }

    public function wishlistDelete($id)
    {
        $com = Wishlist::where('propertyId',$id)->where('userId', Auth::user()->id)->delete();
        return back()->with('success', 'Property deleted successufully');
    }
}