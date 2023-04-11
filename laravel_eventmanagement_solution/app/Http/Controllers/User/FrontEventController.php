<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventCalendar;
use App\Models\EventCategory;
use App\Models\EventManage;
use App\Models\EventTicket;
use App\Models\FoodItems;
use App\Models\FoodStore;
use Carbon\Carbon;
use Google\Service\CloudSearch\PushItem;
use Google\Service\Docs\Request as DocsRequest;
use Illuminate\Support\Facades\DB;
use phpseclib3\Crypt\RC2;

class FrontEventController extends Controller
{
    public function index(Request $request, $type = null)
    {
        $recentEventDetail = Event::where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->latest()->take(6)->get();
        $eventDetail = Event::query();
        $eventCategory = EventCategory::where('status', '1')->get();
        $eventCity = Event::select('city')
            ->groupBy('city')
            ->get();
        if ($request->has('event')) {
            $eventDetail->whereHas('category', function ($query) use ($request) {
                $query->where('id', $request->event);
            });
        }
        if ($request->flag == 1) {
            if (($request->type != '' || $request->category != '' || $request->city != '')) {
                $type = isset($request->type) ? $request->type : '';
                $city = isset($request->city) ? $request->city : '';
                // echo $request->city;
                $category = isset($request->category) ? $request->category : '';
                if (!empty($type)) {
                    $eventDetail = $eventDetail->where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->whereIn('event_type', $type)->paginate(5);
                }
                elseif (!empty($city)) {
                    // echo 1;
                    $eventDetail = $eventDetail->where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->whereIn('city', $city)->paginate(5);
                    // print_r($eventDetail); die;
                } elseif (!empty($category)) {
                    // echo 1;
                    $eventDetail = $eventDetail->where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->whereIn('category_id', $category)->paginate(5);
                }  else {
                    // echo 2;
                    $eventDetail = $eventDetail->where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->whereIn('event_type', $type)->whereIn('city', $city)->paginate(5);
                    // print_r($eventDetail); die;

                }
                $returnHTML = view('User/Pages/Event/EventFilter', compact('eventDetail', 'recentEventDetail', 'eventCategory', 'eventCity'))->render();
                return response()->json(array('success' => true, 'html' => $returnHTML));
            } else {
                $eventDetail = $eventDetail->where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->paginate(5);
                $returnHTML = view('User/Pages/Event/EventFilter', compact('eventDetail', 'recentEventDetail', 'eventCategory', 'eventCity'))->render();
                return response()->json(array('success' => true, 'html' => $returnHTML));
            }
        } else {
            $eventDetail = $eventDetail->where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->paginate(5);

            return view('User/Pages/Event/Index', compact('eventDetail', 'recentEventDetail', 'eventCategory', 'eventCity'));
        }
    }

    public function show($id)
    {
        $eventDetail = Event::where('id', $id)->first();
        $foodStoreid = DB::table('eventfoodstore')->where('event_id', $id)->pluck('food_store_id')->toArray();
        $foodStore = FoodStore::whereIn('id', $foodStoreid)->get();
        $artist = DB::table('eventaritst')->where('event_id', $id)->pluck('artist_id')->toArray();
        $artists = Artist::where('id', $artist)->get();
        // echo $artists; die;
        $start = Carbon::parse($eventDetail->start_time);
        $end = Carbon::parse($eventDetail->end_time);
        $hours = $end->diffInHours($start);
        $eventId = $id;
        $currency = Currency::all();
        $eventTicket = EventTicket::where('event_id', $eventId)->get();
        $recentEventDetail = Event::where('start_date', '>=', date('Y-m-d'))->where('is_varified', '1')->latest()->take(6)->get();

        return view('User/Pages/Event/Show', compact('eventDetail', 'recentEventDetail', 'foodStore', 'eventTicket', 'hours', 'currency','artists', 'eventId'));
    }

    public function FoodStoreDetail($id)
    {
        // $foodStoreid = DB::table('eventfoodstore')->where('event_id', $id)->pluck('food_store_id')->toArray();
        $foodStore = FoodStore::where('id', $id)->first();
        // dd($foodStore);
        $foodmenu = DB::table('food_store_menu')->where('food_store_id', $id)->get();
        // dd($foodmenu);
        return view('User/Pages/Event/FoodStoreDetail', compact('foodStore', 'foodmenu'));
    }


    public function add(Request $request)
    {
        // dd($request);
        $ticketdata = $request->data;
        $inlcudeFeePrice = 0;
        $items = [];
        foreach ($ticketdata as $key => $data) {
            $quantity = $data['event_cat_tickets'];
            $category = $data['event_cat_id'];
            // dd($category);
            $eventCat = EventTicket::with('ticketCategories')->findOrFail($category);
            $total_price = $quantity * $eventCat->ticket_price;
            // dd($total_price);
            array_push($items, [
                'event_category_id' => $category,
                'event_cat_name' => $eventCat->ticketCategories->name,
                'event_category_tickets' => $quantity,
                'ticket_price' => $total_price,
            ]);
            $inlcudeFeePrice += $total_price;
        }

        if ($inlcudeFeePrice <= 500) {
            $fee = 30;
        } else if ($inlcudeFeePrice > 500 && $inlcudeFeePrice <= 1000) {
            $fee = 60;
        } else {
            $fee = 100;
        }
        $grandTotal = $inlcudeFeePrice + $fee;

        if ($request->ajax()) {
            return response()->json([
                'grandTotal' => $grandTotal,
                'fee' => $fee,
                'items' => $items,
            ]);
        }
    }

    public function viewLoad(Request $request)
    {
        // dd($request);
        $ticketdata = $request->data;
        $inlcudeFeePrice = 0;
        $items = [];
        $catIds = '';
        $catTickets = '';
        foreach ($ticketdata as $key => $data) {
            $quantity = $data['event_cat_tickets'];
            $category = $data['event_cat_id'];
            $catIds .= $data['event_cat_id'] . ",";
            $catTickets .= $data['event_cat_tickets'] . ",";
            $eventCat = EventTicket::with('ticketCategories')->findOrFail($category);
            $total_price = $quantity * $eventCat->ticket_price;
            array_push($items, [
                'event_category_id' => $category,
                'event_cat_name' => $eventCat->ticketCategories->name,
                'event_category_tickets' => $quantity,
                'item_price' => $total_price,
            ]);
            $inlcudeFeePrice += $total_price;
        }

        if ($inlcudeFeePrice <= 500) {
            $fee = 30;
        } else if ($inlcudeFeePrice > 500 && $inlcudeFeePrice <= 1000) {
            $fee = 60;
        } else {
            $fee = 100;
        }
        $grandTotal = $inlcudeFeePrice + $fee;
        $eventId = $request->event_id;
        if ($request->ajax()) {
            $view = view('User.Pages.Event.razorpay', compact('grandTotal', 'eventId', 'fee', 'catIds', 'catTickets'))->render();
            return response()->json(['view' => $view]);
        }
    }
    
}
