<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\PropertyManager;
use App\Models\Currency;
use App\Models\PopularDestination;
use App\Models\Propertytype;
use App\Models\User;

use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $currency = Currency::all()->where('default', 1)->first();
        $ptype = Propertytype::all();

        if ($request->has('product_search')) {
            $wishlist = PropertyManager::where('reason', 'sell')->pluck('id')->toarray();
            $products = PropertyManager::whereIn('id', $wishlist)->where('address', 'ILIKE', '%' . $request->product_search . '%')->where('title', 'ILIKE', '%' . $request->product_search . '%')
                ->paginate(7);
        } else {
            $products = PropertyManager::paginate(7);
        }
        return view('user/pages/search', compact('products', 'currency', 'ptype'));
    }
    public function indexRent(Request $request)
    {
        $currency = Currency::all()->where('default', 1)->first();
        $ptype = Propertytype::all();

        if ($request->has('product_search')) {
            $wishlist = PropertyManager::where('reason', 'rent')->pluck('id')->toarray();
            $products = PropertyManager::whereIn('id', $wishlist)->where('address', 'ILIKE', '%' . $request->product_search . '%')->where('title', 'ILIKE', '%' . $request->product_search . '%')
                ->paginate(7);
        } else {
            $products = PropertyManager::paginate(7);
        }
        return view('user/pages/search', compact('products', 'currency', 'ptype'));
    }

    public function Detail($id)
    {
        $data = PropertyManager::find($id);
        return view('user/pages/searchdetail', compact('data'));
    }

    public function indexPera(Request $request)
    {
        $products = PropertyManager::where('ptype', $request->ptype)->get()->where('totalPrice', '<=', $request->price)->where('bedroom', '>=', $request->minbedroom)->where('bedroom', '<=', $request->minbedroom)->where('bathroom', '=', $request->bathroom);

        $currency = Currency::all()->where('default', 1)->first();

        $ptype = Propertytype::all();

        return view('user/pages/search', compact('products', 'currency', 'ptype'));
    }

    public function MapView($citydata)
    {
        $data = PropertyManager::where('city', $citydata)->get();
        return view('user/pages/mapView/mapview', compact('data'));
    }

    public function searchCity(Request $request)
    {
        $id = app('request')->input('id');
        $dest = DB::table('popular_destinations')->where('id', $id)->pluck('title')->toArray();
        $searchCity = PropertyManager::all();
        $currency = Currency::get()->where('default', 1);
        $city = PopularDestination::all();
        $ptype = Propertytype::all();

        return view('user/pages/searchcity', compact('dest', 'searchCity', 'currency', 'city', 'ptype'));
    }
    public function RentProperties(Request $request)
    {
        $rentProperty = PropertyManager::where('reason', $request->type)->where('status', 1)->get();
        $currency = Currency::get()->where('default', 1);
        return view('user/pages/property/propertyreason', compact('rentProperty', 'currency'));
    }

    public function BuyProperties(Request $request)
    {
        $rentProperty = PropertyManager::where('reason', $request->type)->where('status', 1)->get();
        $currency = Currency::get()->where('default', 1);
        return view('user/pages/property/propertyreason', compact('rentProperty', 'currency'));
    }

    public function AgentSearch(Request $request)
    {
        $data = User::where('type', 2)
            ->where('name', 'ILIKE', '%' . $request->agent_query . '%')
            // ->orWhere('address', 'ILIKE', '%' . $request->agent_query . '%')
            // ->orWhere('city', 'ILIKE', '%' . $request->agent_query . '%')
            // ->orWhere('state', 'ILIKE', '%' . $request->agent_query . '%')
            // ->orWhere('country', 'ILIKE', '%' . $request->agent_query . '%')
            // ->orWhere('postal_code', 'ILIKE', '%' . $request->agent_query . '%')
            ->get();
        return view('user/pages/searchAgentReasult', compact('data'));
    }
}
