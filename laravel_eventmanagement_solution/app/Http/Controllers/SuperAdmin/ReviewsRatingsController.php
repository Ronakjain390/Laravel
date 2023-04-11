<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReviewsRating;

class ReviewsRatingsController extends Controller
{
    public function index()
    {
        $data = ReviewsRating::all();
        return view('SuperAdmin/Pages/ReviewsRatings/Index', compact('data'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/ReviewsRatings/Create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'reviews' => 'required',
        ]);

        $file_name = 'null';
        if ($request->image == 'image') {
            $file_name = time() . '.' . request()->file('image')->getClientOriginalExtension();

            request()->image->move(public_path('Assets/SuperAdmin/img/ReviewsRatings/'), $file_name);
        }

        $reviewsrating = new ReviewsRating;
        $reviewsrating->name = $request->name;
        $reviewsrating->reviews = $request->reviews;
        $reviewsrating->rating = $request->rating;
        $reviewsrating->image = $file_name;
        $reviewsrating->is_approve = $request->is_approve;
        if ($request->status == 'on') {
            $reviewsrating->status = 1;
        } else {
            $reviewsrating->status = 0;
        }
        $reviewsrating->save();
        return redirect()->route('ReviewsRatings')
            ->with('success', 'Review/Rating has been created successfully.');
    }

    public function edit($id)
    {
        $data = ReviewsRating::find($id);
        return view('SuperAdmin/Pages/ReviewsRatings/Edit', compact('data'));
    }

    public function show($id)
    {
        $data =ReviewsRating::where('id', $id)->get();
        return view('SuperAdmin/Pages/ReviewsRatings/Show', compact('data'));
    }


    public function update(Request $request, $id)
    {
        $reviewsrating = ReviewsRating::find($id);
        if ($request->action == 'status_toggle') {
            if ($reviewsrating->status == 0) {
                $reviewsrating->status = 1;
            } else {
                $reviewsrating->status = 0;
            }
            $reviewsrating->save();
            return redirect()->route('ReviewsRatings')
                ->with('success', 'Status has been changed successfully.');
        } else {
            $reviewsrating->name = $request->name;
            $reviewsrating->reviews = $request->reviews;
            if ($request->status == 'on') {
                $reviewsrating->status = 1;
            } else {
                $reviewsrating->status = 0;
            }
            $reviewsrating->save();
            return redirect()->route('ReviewsRatings')
                ->with('success', 'Review/Rating Has Been updated successfully');
        }
    }
}
