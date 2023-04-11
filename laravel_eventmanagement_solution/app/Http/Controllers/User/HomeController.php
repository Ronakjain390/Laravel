<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Banner;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function header()
    {
        $artist = Artist::all();
        $eventDetail = Event::where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->distinct()->limit(5)->get();
        $eventCategoryData = EventCategory::where('status', '1')->get();
        $upcomingEvents  = Event::where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->take(3)->get();
        // echo $eventCategoryData; die;
        $banner = Banner::where('status', '1')->get();
        // echo $banner; die;
        return view('User/Pages/Index', compact('artist', 'eventDetail', 'banner', 'eventCategoryData', 'upcomingEvents'));
    }

    public function search(Request $request, $type = null)
    {
        $request->validate(
            [
                'search' => 'required'
            ],
            [
                'search' => 'Please enter value'
            ]
        );

        $search = $request->search;
        $artist_count = Artist::where('name', 'ILIKE', '%' . $search . '%')->count();

        $artist = Artist::all()->toArray();
        $eventDetailCount = Event::where('title', 'ILIKE', '%' . $search . '%')->count();
        $eventDetail = Event::inRandomOrder()->limit(3)->get();
        $eventDetail = Event::where('title', 'ILIKE', '%' . $search . '%')->get();
        $artist = Artist::where('name', 'ILIKE', '%' . $search . '%')->get();

        return view('User/Pages/Search', compact('artist', 'eventDetail', 'artist_count', 'eventDetailCount'));

        // if ($eventDetailCount > 0) {
        //     return view('User/Pages/Search', compact('eventDetail', 'artist'));
        // } elseif ($artist_count > 0) {

        //     $artist = Artists::where('name', $search)->get();
        //     $eventDetail = Event::where('title', $search)->get();
        //     return view('User/Pages/Search', compact('artist', 'eventDetail'));
        // } else {
        //     return view('User/Pages/Search', compact('artist', 'eventDetail'));
        // }
    }
}
