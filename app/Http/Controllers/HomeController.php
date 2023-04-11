<?php

namespace App\Http\Controllers;

use App\Models\GeneralSettingModel;
use App\Models\Banners;
use App\Models\Currency;
use App\Models\News;
use App\Models\PopularDestination;
use App\Models\PropertyManager;
use App\Models\Propertyenquiry;
use App\Models\Propertytype;
use App\Models\Amenities;
use App\Models\Flags;
use App\Models\Propertycategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        $ip = request()->ip();
        $location = Http::get("https://get.geojs.io/v1/ip/geo/" . "61.247.235.126" . ".json");

        $propertyFlag = Flags::where('flag_name', 'ILIKE', '%' . 'Ready to Shift' . '%')->get()->first();
        $propertyFlag2 = Flags::where('flag_name', 'ILIKE', '%' . 'Under Development' . '%')->get()->first();
        $property3 = Propertycategory::where('category_name', 'ILIKE', '%' . 'Commercial' . '%')->get()->first();
        // echo $propertyFlag, "hello";
        // die;
        if ($propertyFlag) {
            $property = PropertyManager::where('propertyflag', $propertyFlag->id)->where('status', 1)->latest()->take(4)->get();
        } else {
            $property = [];
        }
        if ($propertyFlag2) {
            $property2 = PropertyManager::where('propertyflag', $propertyFlag2->id)->where('status', 1)->latest()->take(4)->get();
        } else {
            $property2 = [];
        }
        if ($property3) {
            $property3 = PropertyManager::where('propertycategory', $property3->id)->where('status', 1)->latest()->take(4)->get();
        } else {
            $property3 = [];
        }

        $property4 = Propertycategory::where('category_name', 'ILIKE', '%' . 'PG' . '%')->get()->first();

        $propertyBuy = PropertyManager::where('reason',  'ILIKE', '%' . 'Buy' . '%')->where('status', 1)->latest()->take(4)->get();
        $propertyRent = PropertyManager::where('reason',  'ILIKE', '%' . 'Rent' . '%')->where('status', 1)->latest()->take(4)->get();

        $propertyBuytab = PropertyManager::where('reason',  'ILIKE', '%' . 'Buy' . '%')->where('status', 1)->latest()->take(4)->get();
        $propertyRenttab = PropertyManager::where('reason',  'ILIKE', '%' . 'Rent' . '%')->where('status', 1)->latest()->take(4)->get();
        if ($property4) {
            $propertyPGtab = PropertyManager::where('propertycategory',  $property4->id)->where('status', 1)->latest()->take(4)->get();
        } else {
            $propertyPGtab = [];
        }
        if ($property4) {
            $propertyPG = PropertyManager::where('propertycategory',  $property4->id)->where('status', 1)->latest()->take(4)->get();
        } else {
            $propertyPG = [];
        }
        $city = PopularDestination::all();
        $banner = Banners::all()->where('status', 1);
        $currency = Currency::all()->where('default', 1)->first();
        $logo = GeneralSettingModel::all();
        $news = News::latest()->take(4)->get()->where('status', 1);
        $ptype = Propertytype::where('status', 1)->get();
        $ameneties = Amenities::where('status', 1)->get();
        return view('user/pages/index', compact('banner', 'logo', 'property', 'currency', 'news', 'city', 'ptype', 'ameneties', 'location', 'property2', 'property3', 'propertyBuy', 'propertyRent', 'propertyPG', 'propertyBuytab', 'propertyRenttab', 'propertyPGtab'));
    }

    public function agentHome()
    {
        $agentrent = PropertyManager::get()->where('agentid', Auth::user()->id)->where('status', 1)->where('Utype', 'agent')->where('reason', 'rent')->count();
        $agentsell = PropertyManager::get()->where('agentid', Auth::user()->id)->where('status', 1)->where('Utype', 'agent')->where('reason', 'sell')->count();
        $pro = PropertyManager::get()->where('agentid', Auth::user()->id)->pluck('id');
        $enquiry = Propertyenquiry::get()->whereIn('propertyId', $pro)->count();

        return view('agent/pages/home', compact('agentrent', 'agentsell', 'enquiry'));
    }
}
