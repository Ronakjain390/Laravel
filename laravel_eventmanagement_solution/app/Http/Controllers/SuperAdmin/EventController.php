<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artists;
use App\Models\BookingTicket;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventTicket;
use App\Models\EventTicketCategory;
use App\Models\FoodStore;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class EventController extends Controller
{
    public function index()
    {
        $eventData = Event::where('is_varified', 1)->get();
        $user = User::where('type', 0)->first();
        $eventCategoryData = EventCategory::all();
        $tickets = EventTicket::all();
        // echo $tickets; die;
        return view('SuperAdmin/Pages/Event/Index', compact('eventData', 'eventCategoryData', 'tickets', 'user'));
    }

    public function create()
    {
        $foodstore = FoodStore::where('status', 1)->get();
        $eventCategoryData = EventCategory::where('status', 1)->get();
        $artist = Artist::where('status', 1)->get();
        $ticket_catagory = TicketCategory::where('status', 1)->get();
        return view('SuperAdmin/Pages/Event/Create', compact('eventCategoryData', 'foodstore', 'artist', 'ticket_catagory'));
    }

    public function store(Request $request)
    {
        // dd($request);


        $image = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('Assets/images/'), $image);

        $event = new Event;
        $event->user_id = Auth::user()->id;
        $event->title = $request->title;
        $event->email = $request->email;
        $event->contact_number = $request->contact_number;
        $event->start_date = dmy_to_yyymmdd($request->start_date);
        $event->end_date = dmy_to_yyymmdd($request->end_date);
        $event->start_time = $request->start_time;
        $event->end_time = $request->end_time;
        $event->duration = $request->duration;
        $event->category_id = $request->category_id;
        $event->event_type = $request->event_type;
        if ($request->hasfile('gallary_image')) {
            foreach ($request->file('gallary_image') as $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path('Assets/images'), $name);
                $imgData[] = $name;
            }

            $event->gallary_images = json_encode($imgData);
        }

        $event->is_varified = 1;
        // $event->foodstoreid = $request->foodstore;


        if ($request->event_type == 0) {
            $event->venue = $request->venue;
            $event->address = $request->address;
            $event->city = $request->city;
            $event->state = $request->state;
            $event->country = $request->country;
            $event->postal_code = $request->postal_code;
            if ($request->longitude) {
                $event->longitude = $request->longitude;
                $event->latitude = $request->latitude;
            }
        }

        if ($request->event_type == 1) {
            $event->meeting_link = $request->meeting_link;
        }

        if ($request->event_type == 2) {
            $event->venue = $request->venue;
            $event->address = $request->address;
            $event->city = $request->city;
            $event->state = $request->state;
            $event->country = $request->country;
            $event->postal_code = $request->postal_code;
            if ($request->longitude) {
                $event->longitude = $request->longitude;
                $event->latitude = $request->latitude;
            }

            $event->meeting_link = $request->meeting_link;
        }


        $event->about = $request->about;
        $event->image = $image;
        if ($request->status == 'on') {
            $event->status = 1;
        } else {
            $event->status = 0;
        }
        $event->save();

        $event_id = $event->id;

        $artist = $request->artist;
        if (count($artist) > 0) {
            foreach ($artist as $art) {
                DB::table('eventaritst')->insert([
                    'artist_id' => $art,
                    'event_id' => $event_id
                ]);
            }
        }

        $foods = $request->foodstore;
        if ($request->event_type != 1 && count($foods) > 0) {
            foreach ($foods as $food) {
                DB::table('eventfoodstore')->insert([
                    'food_store_id' => $food,
                    'event_id' => $event_id
                ]);
            }
        }

        $ticket_cat = $request->ticket_cat;
        $ticket_qty = $request->ticket_qty;
        $ticket_price = $request->ticket_price;

        $insert_data = [];
        for ($i = 0; $i < count($ticket_cat); $i++) {
            if (isset($ticket_cat[$i]) && isset($ticket_qty[$i]) && isset($ticket_price[$i])) {
                $data = array(
                    'ticket_category_id' => $ticket_cat[$i],
                    'no_of_ticket'  => $ticket_qty[$i],
                    'ticket_price'  => $ticket_price[$i],
                    'event_id' => $event_id
                );
                $insert_data[] = $data;
            }
        }

        DB::table('eventtickets')->insert($insert_data);

        // Mail::send('EventOrgnizer/Auth/emailverify', compact('token'), function ($message) use ($request) {
        //     $message->to($request->email);
        //     $message->subject('Email Verification Mail');
        // });

        return redirect()->route('Events')
            ->with('success', 'Event has been created successfully. ');
    }

    public function show($id)
    {
        $event = Event::where('id', $id)->first();

        $eventCategory = EventCategory::where('id', $event->category_id)->first();
        $eventCategoryAll = EventCategory::where('status', '1')->get();

        $eventArtist1 = DB::table('eventaritst')->where('event_id', $id)->pluck('artist_id')->toArray();
        $eventArtist = Artist::whereIn('id', $eventArtist1)->get();

        $eventFoodStore1 = DB::table('eventfoodstore')->where('event_id', $id)->pluck('food_store_id')->toArray();
        $eventFoodStore = FoodStore::whereIn('id', $eventFoodStore1)->get();

        $tickets = TicketCategory::where('status', 1)->get();
        $eventTicket = DB::table('eventtickets')->where('event_id', $id)->get();

        return view('SuperAdmin/Pages/Event/Edit', compact('event', 'eventCategory', 'eventArtist', 'eventFoodStore', 'eventTicket', 'tickets'));
    }

    public function edit($id)
    {
        $event = Event::where('id', $id)->first();

        $eventCategory = EventCategory::where('id', $event->category_id)->first();
        $eventCategoryAll = EventCategory::where('status', '1')->get();

        $eventArtist1 = DB::table('eventaritst')->where('event_id', $id)->pluck('artist_id')->toArray();
        $eventArtist = Artist::whereIn('id', $eventArtist1)->get();

        $eventFoodStore1 = DB::table('eventfoodstore')->where('event_id', $id)->pluck('food_store_id')->toArray();
        $eventFoodStore = FoodStore::whereIn('id', $eventFoodStore1)->get();

        $tickets = TicketCategory::where('status', 1)->get();
        $eventTicket = DB::table('eventtickets')->where('event_id', $id)->get();

        $food_store = FoodStore::where('status', 1)->get();
        $artist = Artist::where('status', 1)->get();
        $ticket_cat = TicketCategory::where('status', 1)->get();
        return view('SuperAdmin/Pages/Event/Edit', compact('event', 'eventCategory', 'eventArtist', 'eventFoodStore', 'eventTicket', 'tickets', 'eventCategoryAll', 'food_store', 'artist', 'ticket_cat'));
    }

    public function update(Request $request, $id)
    {
        $eventUpdate = Event::find($id);

        $image = $request->hidden_Image;
        if ($request->image != '') {
            $image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images/'), $image);
        }
        if ($request->hasfile('gallary_image')) {
            foreach ($request->file('gallary_image') as $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path('Assets/images'), $name);
                $imgData[] = $name;
            }

            $eventUpdate->gallary_images = json_encode($imgData);
        }
        $eventUpdate->title = $request->title;
        $eventUpdate->email = $request->email;
        $eventUpdate->image = $image;
        $eventUpdate->contact_number = $request->contact_number;
        $eventUpdate->event_type = $request->event_type;
        $eventUpdate->category_id = $request->category_id;
        $eventUpdate->foodstoreid = $request->foodstore;
        $eventUpdate->venue = $request->venue;
        $eventUpdate->about = $request->about;
        $eventUpdate->price = $request->price;
        $eventUpdate->start_date = dmy_to_yyymmdd($request->start_date);
        $eventUpdate->end_date = dmy_to_yyymmdd($request->end_date);
        $eventUpdate->address = $request->address;
        $eventUpdate->city = $request->city;
        $eventUpdate->state = $request->state;
        $eventUpdate->country = $request->country;
        $eventUpdate->postal_code = $request->postal_code;
        $eventUpdate->start_time = $request->start_time;
        $eventUpdate->end_time = $request->end_time;
        $eventUpdate->latitude = $request->latitude;
        $eventUpdate->longitude = $request->longitude;
        if ($request->status == 'on') {
            $eventUpdate->status = 1;
        } else {
            $eventUpdate->status = 0;
        }
        $eventUpdate->save();
        return redirect()->route('Events')
            ->with('success', 'Event Category has been created successfully.');
    }

    public function delete($id)
    {
        // echo '1'; die;
        $eventCategoryDelete = Event::where('id', $id)->delete();
        return redirect()->route('Events')->with('error', 'Events  deleted successfully');
    }


    public function ticketShowEvent($id)
    {
        $bookingTicket = BookingTicket::where('event_id', $id)->get()->groupBy(function($bookingTicket) {
            return $bookingTicket->category_id;
        });
        $category = TicketCategory::all();
        $totalTicket = EventTicket::where('event_id', $id)->get();
        // dd($bookingTicket)->toArray();
        $event = Event::where('user_id', Auth::user()->id)->get();



        return view('SuperAdmin/Pages/Event/TicketListing', compact('bookingTicket', 'category', 'totalTicket'));
    }
}
