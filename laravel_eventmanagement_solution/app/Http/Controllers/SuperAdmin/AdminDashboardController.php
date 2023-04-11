<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\BookingTicket;
use App\Models\Currency;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventTicket;
use App\Models\Expertize;
use App\Models\FoodStore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()

    {
        $currency = Currency::pluck('currency_symbol');
        $totalUsers = User::where('type', 1)->count();
        $totalOrganizers = User::where('type', 2)->count();
        // print_r($currency); die;
        $totalEvents = Event::where('status', '1')->count();
        $foodStore = FoodStore::count();
        $artist = Artist::count();
        $event_id = Event::where('user_id',Auth::user()->id)->pluck('id')->toArray();
        $totalAmount = BookingTicket::whereIn('event_id',$event_id)->sum('amount');
        $ticketSold = EventTicket::whereIn('event_id',$event_id)->sum('booked_tickets');
        $artistExpertize = Expertize::all()->count();
        $eventCategories = EventCategory::count();


        return view('SuperAdmin/Pages/Index' ,compact('totalEvents','totalAmount', 'foodStore', 'artist', 'ticketSold', 'currency', 'artistExpertize', 'eventCategories', 'totalUsers', 'totalOrganizers'));
    }
}
