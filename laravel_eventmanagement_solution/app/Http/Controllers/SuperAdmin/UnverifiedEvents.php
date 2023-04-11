<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\FoodStore;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UnverifiedEvents extends Controller
{
    function index()
    {
        $events = Event::where('is_varified', 0)->get();

        return view('SuperAdmin/Pages/UnverifiedEvents/index', compact('events'));
    }

    function verify($id)
    {
        Event::where('id', $id)->update([
            'is_varified' => 1
        ]);

        $event = Event::select('title', 'email')->where('id', $id)->first();

        Mail::send('SuperAdmin/Pages/UnverifiedEvents/verifytemplate', compact('event'), function ($message) use ($event) {
            $message->to($event->email);
            $message->subject('Event Verified by Admin Mail');
        });
        return back()->with('success', "Event has been succesfully verified!!");
    }

    function delete($id)
    {
        $event = Event::select('title', 'email')->where('id', $id)->first();

        Event::where('id', $id)->delete();

        Mail::send('SuperAdmin/Pages/UnverifiedEvents/deletetemplate', compact('event'), function ($message) use ($event) {
            $message->to($event->email);
            $message->subject('Event denied by Admin Mail');
        });
        return back()->with('error', "Event has been deleted succesfully!!");
    }

    function show($id)
    {
        $event = Event::where('id', $id)->first();
        $user = User::where('id', $event->user_id)->first();
        $eventCategory = EventCategory::where('id', $event->category_id)->first();
        $eventArtist1 = DB::table('eventaritst')->where('event_id', $id)->pluck('artist_id')->toArray();
        $eventArtist = Artist::whereIn('id', $eventArtist1)->get();
        $eventFoodStore1 = DB::table('eventfoodstore')->where('event_id', $id)->pluck('food_store_id')->toArray();
        $eventFoodStore = FoodStore::whereIn('id', $eventFoodStore1)->get();

        $tickets = TicketCategory::where('status', 1)->get();
        $eventTicket = DB::table('eventtickets')->where('event_id', $id)->get();

        return view('SuperAdmin/Pages/UnverifiedEvents/preview', compact('event', 'user', 'eventCategory', 'eventArtist', 'eventFoodStore', 'eventTicket', 'tickets'));
    }

    function unverify($id)
    {
        Event::where('id', $id)->update([
            'is_varified' => 0
        ]);

        $event = Event::select('title', 'email')->where('id', $id)->first();

        Mail::send('SuperAdmin/Pages/UnverifiedEvents/unverifytemplate', compact('event'), function ($message) use ($event) {
            $message->to($event->email);
            $message->subject('Event Verified by Admin Mail');
        });
        return back()->with('success', "Event has been un-verified!!");
    }
}
