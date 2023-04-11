<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\TicketBookedMail;
use App\Models\BookingTicket;
use App\Models\Currency;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Session;
use Redirect;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventManage;
use App\Models\EventTicket;
use App\Models\FoodStore;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Stmt\Echo_;

class RazorpayController extends Controller
{
    public function razorpay(Request $request)
    {
        // dd($request);
        $quantity =  $request->event_cat_tickets;
        $category = $request->ticket_category_id;
        $id = $request->event_id;
        $price = EventTicket::where('event_id', $id)->where('ticket_category_id', $category)->first()->price;

        $total_price  = $quantity * $price;


        if ($price <= 500) {
            $fee = 30;
        } else if ($price > 500 && $price <= 1000) {
            $fee = 60;
        } else {
            $fee = 100;
        }
        $amount =  $total_price + $fee;
        Session::put('amount', $amount);
        return view('User/Pages/Buy/Razorpay', compact('quantity', 'category', 'price', 'total_price', 'fee', 'amount', 'id'));
    }

    public function payment(Request $request)
    {
        // dd($request->all());
        $input = $request->all();
        $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        $catId = explode(',', $payment->notes->catIds);
        $end = count($catId) - 1;
        unset($catId[$end]);
        $catTicket = explode(',', $payment->notes->catTickets);
        $end = count($catTicket) - 1;
        unset($catTicket[$end]);


        // dd($catId);

        if (count($input)  && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                foreach ($catId as $key => $category) {
                    $manage = EventTicket::find($category);
                    // echo $manage; die;
                    // dd($manage);
                    $booking = new BookingTicket();
                    $booking->user_id = auth('web')->id();
                    $booking->category_id = $manage->ticket_category_id;
                    $booking->price = $manage->ticket_price;

                    if ($booking->price <= 500) {
                        $fee = 30;
                    } else if ($booking->price > 500 && $booking->price <= 1000) {
                        $fee = 60;
                    } else {
                        $fee = 100;
                    }
                    $booking->fee = $fee;
                    $booking->event_id = $payment->notes->eventId;
                    $booking->quantity = $catTicket[$key];
                    if ($booking->quantity == '0') {
                        continue;
                    }
                    $booking->payment_id = $request->razorpay_payment_id;
                    $booking->amount = $booking->quantity * $booking->price;
                    $booking->status = 'Booked';
                    $booking->save();

                    $id = $payment->notes->eventId;
                    $category = $manage->ticket_category_id;
                    $booked_ticket = EventTicket::where('event_id', $id)->where('ticket_category_id', $category)->first();
                    $total = $catTicket[$key] + $booked_ticket->available_tickets;
                    $available = $booked_ticket->no_of_ticket - $total;
                    // print_r($available); die;
                    $updateTickets = EventTicket::where('event_id', $id)->where('ticket_category_id', $category)->update(['booked_tickets' => $total, 'available_tickets' => $available]);
                }

                // $booked_ticket = new EventManage();
                // $booked_ticket->booked_tickets  = $catTicket[$key];
                // $booked_ticket->save();

                // $id = $payment->notes->eventId;
                // $category = $manage->category_id;
                // $booked_ticket = EventManage::where('event_id',$id)->where('category_id', $category)->first();

                // $total = $total_users + $booked_ticket->booked_tickets;
                // $updateTickets = EventManage::where('event_id', $id)->where('category_id', $category)->update(['booked_tickets' => $total]);
                $recipient = 'ronak.j@addwebsolution.in';
                $name = 'Ronak Jain';
                $ticket = 'Your ticket is booked';
                Mail::to($recipient)->send(new TicketBookedMail($name, $ticket));
            } catch (\Exception $e) {
                return  $e->getMessage();
                \Session::put('error', $e->getMessage());
                return redirect()->back();
            }
        }
        $eventDetail = Event::where('id', $payment->notes->eventId)->first();
        $foodStore = FoodStore::all();
        $start = Carbon::parse($eventDetail->start_time);
        $end = Carbon::parse($eventDetail->end_time);
        $hours = $end->diffInHours($start);
        $eventId =  $payment->notes->eventId;
        $currency = Currency::all();
        $eventTicket = EventTicket::where('event_id', $eventId)->get();

        // \Session::put('success', 'Payment successful, Your Ticket is Booked Succefully');
        
        return redirect()->route('TicketListing')->with('success', 'Payment successful, Your Ticket is Booked Succefully');
    }
    public function update(Request $request, $id)
    {
        $data = BookingTicket::find($id);
        $data->status = $request->status;
        $data->save();
    }

    public function cancelTicekt($id)
    {
        // echo $id; die;
        $ticket = BookingTicket::where('id', $id)->first();
        $updateticket = BookingTicket::where('id', $id)->update(['status' => 'Cancelled']);

        $eventTicket = EventTicket::where('event_id', $ticket->event_id)->first();
        // echo $eventTicket->booked_tickets; die;
        $booked = $eventTicket->booked_tickets - $ticket->quantity;
        $available = $eventTicket->available_tickets + $ticket->quantity;
        // print_r($available); die;
        $updateTickets = EventTicket::where('event_id', $ticket->event_id)->update(['booked_tickets' => $booked, 'available_tickets' => $available]);

        return redirect()->back()->with('success', 'Booking Cancelled Successfully');
    }
}
