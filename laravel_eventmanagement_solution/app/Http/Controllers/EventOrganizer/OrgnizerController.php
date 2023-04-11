<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\BookingTicket;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventTicket;
use App\Models\FoodStore;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\UserVerify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Str;

class OrgnizerController extends Controller
{
    function loginCheck(Request $request)
    {
        if ($request->user()) {
            if ($request->user()->type == 2) {
                return view('EventOrgnizer/Pages/index');
            } elseif ($request->user()->type == 0) {
                return view('SuperAdmin/Pages/Index');
            }
            return redirect()->route('home')->with('success', 'Already Loggedin as a User');
        }
        return view('EventOrgnizer/Auth/login');
    }

    function loginEventOrgnizer(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $remember = $request->remember;

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            if ($request->user()->type == 2) {
                $request->session()->regenerate();
                return redirect()->route('orgnizerdashboard')->with('success', "You have been succesfully loggedin.");
            }
            return back()->with('erros', "You are not authorized User.");
        }

        return back()->with('error', 'Email or UserName or Password may be wrong !!');
    }

    function logoutOrg(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    function index()
    {
        $event = Event::where('user_id', Auth::user()->id)->get();

        return view('EventOrgnizer/Pages/eventlist', compact('event'));
    }

    function create()
    {
        $foodstore = FoodStore::where('status', 1)->get();
        $eventCategoryData = EventCategory::where('status', 1)->get();
        $artist = Artist::where('status', 1)->get();
        $ticket_catagory = TicketCategory::where('status', 1)->get();
        return view('EventOrgnizer/Pages/createevent', compact('eventCategoryData', 'foodstore', 'artist', 'ticket_catagory'));
    }

    public function store(Request $request)
    {
        // dd($request->artist)->toArray();
        $validator = FacadesValidator::make($request->all(), [
            'title' => 'required|max:255',
            'email' => 'required|email',
            'contact_number' => 'required|digits_between:8,12',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            // 'duration' => 'required', 
            'category_id' => 'required | integer',
            'event_type' => 'required | integer',
            'about' => 'required |max:500',
            'image' => 'required| mimes:jpeg,png,jpg,gif | max:2048',
            // 'artist' => 'required',
            'ticket_cat.*' => 'required',
            'ticket_qty.*' => 'required | digits_between:1,99999999999999',
            'ticket_price.*' => 'required | digits_between:1,99999999999999',
            'venue' => 'max:50',
            'address' => 'max:200',
            'city' => 'max:20',
            'state' => 'max:20',
            'country' => 'max:20',

            'postal_code' => 'integer',

            'meeting_link' => 'max:500',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

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

        // $artist = $request->artist;
        // dd(count($request->artist));
        if (count($request->artist) > 1) {
            foreach ($request->artist as $art) {
                DB::table('eventaritst')->insert([
                    'artist_id' => $art,
                    'event_id' => $event_id
                ]);
            }
        }

        $foods = $request->foodstore;
        if (count($foods) > 1) {
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

        return redirect()->route('listevents')
            ->with('success', 'Event has been created successfully and sent to admin for verification.');
    }

    function delete($id)
    {
        Event::find($id)->delete();

        return redirect()->route('listevents')->with('error', 'Event has been created successfully.');
    }

    function edit($id)
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
        return view('EventOrgnizer/Pages/editevent', compact('event', 'eventCategory', 'eventArtist', 'eventFoodStore', 'eventTicket', 'tickets', 'eventCategoryAll', 'food_store', 'artist', 'ticket_cat'));

        // $event = Event::where('id', $id)->first();

        // $foodstore = FoodStore::where('status', 1)->get();
        // $eventCategoryData = EventCategory::where('status', 1)->get();
        // $artist = Artist::where('status', 1)->get();
        // $ticket_catagory = TicketCategory::where('status', 1)->get();

        // $slected_food_store = DB::table('eventfoodstore')->where('event_id', $id)->get();
        // $selected_artist = DB::table('eventaritst')->where('event_id', $id)->get();
        // $selected_tickets = DB::table('eventtickets')->where('event_id', $id)->get();

        // return view('EventOrgnizer/Pages/editevent', compact('event', 'eventCategoryData', 'foodstore', 'artist', 'ticket_catagory', 'slected_food_store', 'selected_artist', 'selected_tickets'));
    }

    function update(Request $request, $id)
    {
        if (isset($request->image)) {
            $image = time() . '.' . request()->file('image')->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images.'), $image);
        } else {
            $oldimage = Event::find($id)->first();
            $image = $oldimage->image;
        }
        
        if ($request->status == 'on') {
            $status = 1;
        } else {
            $status = 0;
        }
        $event = Event::find($id);
        $event->title = $request->title;
        $event->email = $request->email;
        $event->contact_number = $request->contact_number;

        $event->start_date = $request->start_date;
        $event->end_date = $request->end_date;
        $event->start_time = $request->start_time;
        $event->end_time = $request->end_time;
        $event->duration = $request->duration;
        $event->category_id = $request->category_id;
        $event->event_type = $request->event_type;
        $event->image = $image;
        $event->status = $status;
        $event->about = $request->about;
        if ($request->hasfile('gallary_image')) {
            foreach ($request->file('gallary_image') as $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path('Assets/images'), $name);
                $imgData[] = $name;
            }

            $event->gallary_images = json_encode($imgData);
        }

        if ($request->event_type == 0 || $request->event_type == 2) {
            $event->venue = $request->venue;
            $event->address = $request->address;
            $event->city = $request->city;
            $event->state = $request->state;
            $event->country = $request->country;
            $event->postal_code = $request->postal_code;
            if ($request->longitude) {
                $event->longitude = $request->longitude;
            }
            if ($request->latitude) {
                $event->latitude = $request->latitude;
            }
        }

        if ($request->event_type == 1 || $request->event_type == 2) {
            $event->meeting_link = $request->meeting_link;
        }

        $event->save();

        $event_id = $event->id;


        if ($request->food_store_id) {
            // DB::table('eventfoodstore')->where('event_id', $event_id)->delete();
            foreach ($request->food_store_id as $food) {
                DB::table('eventfoodstore')->insert([
                    'food_store_id' => $food,
                    'event_id' => $event_id
                ]);
            }
        }

        if ($request->artist_id) {
            // DB::table('eventaritst')->where('event_id', $event_id)->delete();
            foreach ($request->artist_id as $art) {
                DB::table('eventaritst')->insert([
                    'artist_id' => $art,
                    'event_id' => $event_id
                ]);
            }
        }

        $ticket_cat = $request->ticket_cat;
        $ticket_qty = $request->ticket_qty;
        $ticket_price = $request->ticket_price;

        // dd($ticket_qty);

        $insert_data = [];
        for ($i = 0; $i < count($ticket_cat); $i++) {
            DB::table('eventtickets')->where('event_id', $event_id)->delete();
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

        return redirect()->route('listevents')->with('success', 'Event has been updated successfully.');
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

        return view('EventOrgnizer/Pages/showevent', compact('event', 'user', 'eventCategory', 'eventArtist', 'eventFoodStore', 'eventTicket', 'tickets'));
    }

    function profile()
    {
        $user = User::where('id', Auth::user()->id)->get();
        return view('EventOrgnizer/Pages/profile', compact('user'));
    }

    function profileImageDelete()
    {
        User::where('id', Auth::user()->id)->update([
            'image' => null
        ]);

        // session()->pull('image', null);

        return redirect()->back()->with('error', 'Profile Image deleted succesfully.');
    }

    function profileupdate(Request $request)
    {
        if (isset($request->image)) {
            $image = time() . '.' . request()->file('image')->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images/'), $image);
        } else {
            $oldimage = User::where('id', Auth::user()->id)->get();
            $image = $oldimage[0]->image;
        }

        User::where('id', Auth::user()->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->contact,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'image' => $image
        ]);

        return redirect()->back()->with('success', 'Profile Updated Successfully');
    }

    function chanegpassword(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'oldpassword' => 'required',
            'newpassword' => 'required|min:8',
            'confirmpassword' => 'required|same:newpassword',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        if (!Hash::check($request->oldpassword, Auth::user()->password)) {
            return redirect()->route('profileorg')->with('error', 'Old Password doesnot match.');
        }
        $passwords = $request->confirmpassword;
        User::where('id', Auth::user()->id)->update([
            'password' => Hash::make($request->confirmpassword)
        ]);

        Mail::send('EventOrgnizer/Auth/passwordchanged', compact('passwords'), function ($message) {
            $message->to(Auth::user()->email);
            $message->subject('Password changed Mail');
        });

        return redirect()->route('profileorg')->with('success', 'Password Changed Succesfully...');
    }


    public function postRegistration(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirmpassword' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $data = $request->post();
        $createUser = $this->createorg($data);
        // dd($createUser->toArray());

        $token = Str::random(64);

        UserVerify::create([
            'user_id' => $createUser->id,
            'token' => $token
        ]);

        Mail::send('EventOrgnizer/Auth/emailverify', compact('token'), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Email Verification Mail');
        });

        return redirect()->route('EventOrg')->with('success', 'A Email verify link has been sent to your email.');
    }


    public function createorg(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => 2
        ]);
    }

    public function verifyAccount($token)
    {
        $verifyUser = UserVerify::where('token', $token)->first();

        $message = 'Sorry your email cannot be identified.';

        if (!is_null($verifyUser)) {
            $user = $verifyUser->user;

            if (!$user->email_verified_at) {
                $verifyUser->user->email_verified_at = 1;
                $verifyUser->user->save();
                $message = "Your e-mail is verified. You can now login with your credentials.";
            } else {
                $message = "Your e-mail is already verified. You can login.";
            }
        }

        return redirect()->route('EventOrg')->with('success', $message);
    }

    function forgotpassword(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $token = Str::random(64);

        DB::table('password_reset')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('EventOrgnizer/Auth/forgotpasswordemail', compact('token'), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Password Reset Link');
        });

        return redirect()->back()->with('success', 'Password Change link has been sent to your email.');
    }


    function verifypassword($token)
    {
        return view('EventOrgnizer/Auth/forgotpasswordvarification', compact('token'));
    }

    function changepassword(Request $request, $token)
    {
        $validator = FacadesValidator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8',
            'confirmpassword' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $updatePassword = DB::table('password_reset')
            ->where([
                'email' => $request->email,
                'token' => $token
            ])
            ->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset')->where(['email' => $request->email])->delete();

        return redirect()->route('EventOrg')->with('success', 'Your password has been changed succesfully!!!');
    }

    public function ticketShow($id)
    {
        $bookingTicket = BookingTicket::where('event_id', $id)->get()->groupBy(function($bookingTicket) {
            return $bookingTicket->category_id;
        });
        $category = TicketCategory::all();
        $totalTicket = EventTicket::where('event_id', $id)->get();
        // dd($bookingTicket)->toArray();
        $event = Event::where('user_id', Auth::user()->id)->get();



        return view('EventOrgnizer/Pages/ticketListing', compact('bookingTicket', 'category', 'totalTicket'));
    }

    function delete_foodstore($id)
    {
        DB::table('eventfoodstore')->where('food_store_id', $id)->delete();

        return back()->with('error', "FoodStore Deleted Succesfully");
    }

    function delete_artist($id)
    {
        DB::table('eventaritst')->where('artist_id', $id)->delete();

        return back()->with('error', "Artist Deleted Succesfully");
    }

    function delete_ticket($id)
    {
        DB::table('eventtickets')->where('id', $id)->delete();

        return back()->with('error', "This Ticket Category Deleted Succesfully");
    }
}
