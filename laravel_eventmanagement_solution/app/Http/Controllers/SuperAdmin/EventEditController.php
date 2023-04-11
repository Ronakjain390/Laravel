<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventEditController extends Controller
{
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

    function event_update(Request $request, $id)
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
        $event->start_time = dmy_to_yyymmdd($request->start_time);
        $event->end_time = dmy_to_yyymmdd($request->end_time);
        $event->duration = $request->duration;
        $event->category_id = $request->category_id;
        $event->event_type = $request->event_type;
        $event->image = $image;
        $event->status = $status;
        $event->about = $request->about;

        if ($request->event_type == 0 || $request->event_type == 2) {
            $event->venue = $request->venue;
            $event->address = $request->address;
            $event->city = $request->city;
            $event->state = $request->state;
            $event->country = $request->country;
            $event->postal_code = $request->postal_code;
            if ($request->longitude) {
                $event->venue = $request->venue;
            }
            if ($request->latitude) {
                $event->venue = $request->venue;
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
            // DB::table('eventtickets')->where('event_id', $event_id)->delete();
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
        return redirect()->route('Events')->with('success', 'Event has been updated successfully.');
    }


    function delete_menu($id)
    {
        DB::table('food_store_menu')->where('id', $id)->delete();

        return back()->with('error', 'Menu updated succesfully');
    }
}
