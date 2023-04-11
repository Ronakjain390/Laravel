<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EventOrganizer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Echo_;

class EventOrganizerController extends Controller
{
    public function index()
    {
        $eventOrganizerData = User::where('type', '2')->get();
        return view('SuperAdmin/Pages/EventOrganizer/Index', compact('eventOrganizerData'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/EventOrganizer/Create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'time_duration' => 'required',
            'about_event' => 'required',
            'language' => 'required',
        ]);
        $image = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('Assets/SuperAdmin/img/eventOrganizer/'), $image);

        $eventOrganizer = new User;
        $eventOrganizer->name = $request->name;
        $eventOrganizer->language = $request->language;
        $eventOrganizer->time_duration = $request->time_duration;
        $eventOrganizer->age = $request->age;
        $eventOrganizer->terms_conditions = $request->terms_conditions;
        $eventOrganizer->about_event = $request->about_event;
        $eventOrganizer->image = $image;

        if ($request->status == 'on') {
            $eventOrganizer->status = 1;
        } else {
            $eventOrganizer->status = 0;
        }
        $eventOrganizer->save();
        return redirect()->route('EventOrganizer')
            ->with('success', 'Event Organizer has been created successfully.');
    }

    public function show($id)
    {
        $eventOrganizerData = User::where('id', $id)->get();
        // print_r($eventOrganizerData);
        // die;
        return view('SuperAdmin/Pages/EventOrganizer/Show', compact('eventOrganizerData'));
    }

    public function edit($id)
    {
        $eventOrganizerData = User::find($id);
        // echo $eventOrganizerData; die;
        return view('SuperAdmin/Pages/EventOrganizer/Edit', compact('eventOrganizerData'));
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

        return redirect()->back()->with('success', 'Profile Updated successfully...');
    }

    public function delete($id)
    {
        $eventOrganizer = User::where('id', $id)->delete();
        return redirect()->route('EventOrganizer')->with('success', 'Organizer deleted successfully');
    }
}
