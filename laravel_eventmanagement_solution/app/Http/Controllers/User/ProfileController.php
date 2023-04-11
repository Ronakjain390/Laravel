<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingTicket;
use App\Models\Currency;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\User;
use Google\Service\CloudSearch\UserId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(){
        $profile = User::all();
        // echo $profile; die;
        return view('User/Pages/Profile/Index', compact('profile'));
    }

    public function edit($id)
    {
        $userProfileEdit = User::find($id);
        return view('User/Pages/Profile/Edit', compact('userProfileEdit'));
    }
    public function update(Request $request, $id)
    {
        // dd($request->all()); 
        $userProfiledupdate = User::find($id);
        $Image1 = User::where('id',$id)->first();


        if ($request->image !=  '') {
            $Image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images'), $Image);
        } else {
            $Image = $Image1->image;
        }

        $userProfiledupdate->image = $Image;
        $userProfiledupdate->name = $request->name;
        $userProfiledupdate->contact = $request->contact;
        $userProfiledupdate->address = $request->address;
        $userProfiledupdate->city = $request->city;
        $userProfiledupdate->state = $request->state;
        // $accountsetting->password = Hash::make($request->password);
        $userProfiledupdate->contact = $request->contact;
        $userProfiledupdate->country = $request->country;
        $userProfiledupdate->postal_code = $request->postal_code;
        // $userProfiledupdate->type = $request->type;
        // $userProfiledupdate->username = $request->username;
        // echo 1; die;
        $userProfiledupdate->save();
        return redirect()->route('UserProfile')
            ->with('success', 'Account Has Been updated successfully');
    }

   

    public function changingPassword(Request $request)
    {
        // dd($request->all()); die;
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required'],
            'confirm_password' => ['same:new_password'],
        ]);
        // echo 1; die;
        // echo $request->current_password , $request->new_password , $request->confirm_password;

        // $password = User::find(auth()->id());
        // echo auth()->user()->password ,"hello", Hash::make($request->current_password);
        // die;
        // if ( Hash::make($request->current_password) == auth()->user()->password) {

        if (!Hash::check(Auth::user()->password , $request->current_password)) {
            // return "hello";
            User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
            return back()->with('success', 'Password Changed Succesfully');
        }
        // return back()->with("error", "Old Password Doesn't match!");
            // return "hi";
    
    }
    public function ticketListing(Request $request)
    {
        $tickets = BookingTicket::where('user_id' ,Auth::user()->id)->get()->groupBy(function($data){
            return $data->event_id;
        });
        // $ticket = BookingTicket::where('user_id' ,Auth::user()->id)->pluck('event_id')->toArray();
        // dd($ticket);
        $eventDetail = Event::where('status', 1)->where('is_varified',1)->get();

        // echo $eventDetail;
        // die;
        // echo $tickets; die;
        return view('User/Pages/Profile/TicketListing', compact('tickets', 'eventDetail'));
    }

    public function ticketListingDetail($id)
    {
        // $tickets = BookingTicket::where('user_id' ,Auth::user()->id)->get();
        $ticket = BookingTicket::where('user_id' ,Auth::user()->id)->where('event_id', $id)->get()->groupBy(function($data){
            return $data->category_id;
        });
        
        // $eventDetail = Event::where('id', $ticket->event_id)->get();
        $category = TicketCategory::where('status', 1)->get();
        // echo ($ticket); die;
        // $currency = Currency::all()->first();
        return view('User/Pages/Profile/TicketListingDetail', compact('ticket','category'));

    }
}
?>