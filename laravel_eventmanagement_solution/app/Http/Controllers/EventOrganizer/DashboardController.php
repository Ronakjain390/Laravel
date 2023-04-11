<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\BookingTicket;
use App\Models\Event;
use App\Models\EventTicket;
use App\Models\FoodStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $totalEvents = Event::where('status', '1')->where('user_id',Auth::user()->id)->count();
        $foodStore = FoodStore::where('user_id',Auth::user()->id)->count();
        $artist = Artist::where('user_id',Auth::user()->id)->count();
        $event_id = Event::where('user_id',Auth::user()->id)->pluck('id')->toArray();
        $totalAmount = BookingTicket::whereIn('event_id',$event_id)->sum('amount');
        $ticketSold = EventTicket::whereIn('event_id',$event_id)->sum('booked_tickets');

        // print_r($totalAmount);die;
        
        
        // dd($totalAmount); 
        return view('EventOrgnizer/Pages/index', compact('totalEvents','totalAmount', 'foodStore', 'artist', 'ticketSold'));
    }
}
