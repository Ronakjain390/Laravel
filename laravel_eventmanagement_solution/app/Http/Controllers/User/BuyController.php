<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventManage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BuyController extends Controller
{
    public function buy(Request $request)
    {
        $eventId = $request->id;
        $event = Event::where('id', $eventId)->get();
        // $event = Event::findOrFail($eventId);
        // echo $eventId; die;
        $eventTicket = EventManage::where('event_id', $eventId)->get(); 
        // $catId = EventManage::where('/', );
       
        return view('User/Pages/Event/Show', compact('eventId', 'eventTicket', 'event', ));
    }

    public function add(Request $request){
        $total_users =  $request->tickets;
        $category = $request->event_type;
        $id = $request->id;
        $price = EventManage::where('event_id', $id)->where('category_id', $category)->first()->price; 

        $total_price  = $total_users * $price;
        
        if($price <= 500){
            $fee = 30;  
        }
        else if($price > 500 && $price <= 1000){
            $fee = 60;
        }
        else{
            $fee = 100;
        }
         $amount =  $total_price + $fee;
         
         if($request->ajax()){
            return response()->json(['amount'=>$amount,'fee'=>$fee,'tickets'=>$total_users]);
         }
        //  echo $amount; die;
        Session::put('amount', $amount);

        return view('User/Pages/Buy/Ticket' , compact('total_users', 'category', 'price', 'id', 'total_price', 'fee', 'amount'));
    }

    public function TicketConfirmation(){
        return view('User/Pages/Buy/TicketConfirmation');
    }

}