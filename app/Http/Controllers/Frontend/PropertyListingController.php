<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use Illuminate\Http\Request;
use App\Models\PropertyManager;
use App\Models\User;
use App\Models\Currency;
use App\Models\Propertytype;


// use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;

class PropertyListingController extends Controller
{
    public function index()
    {
        $currency = Currency::all()->where('default', 1)->first();
        $property = PropertyManager::where('status', 1)->paginate(6);
        $hotProperty = PropertyManager::latest()->take(5)->where('status' ,1)->get();
        return view('user/pages/property.propertylisting', compact('property','currency', 'hotProperty'));
    }

    public function list($id)
    {
        $currency = Currency::all()->where('default', 1)->first();
        $propertylist = PropertyManager::get()->where('id', $id);
        $user = User::get()->where('id', $id);
        $hotProperty = PropertyManager::latest()->take(5)->where('status' ,1)->get();
        $property = PropertyManager::find($id);
        $amenities = Amenities::where('id', $property->generalAmenities)->get()->first();
        return view('user/pages/property.propertydetail', compact('propertylist', 'user', 'currency','hotProperty', 'amenities', 'property'));
    }
    public function Newhomes()
    {
        $newhomes = Propertytype::all()->where('status', 1);

        return view('user/pages/property.newhomes', compact('newhomes'));
    }

    public function propertyType(Request $request)
    { 
        $propertytype = PropertyManager::where('ptype',$request->protype)->where('status', 1)->get();
        $currency = Currency::get()->where('default', 1);
        $ptype = Propertytype::all();

        return view('user/pages/property/propertytype', compact('propertytype','currency','ptype'));
    }
}
