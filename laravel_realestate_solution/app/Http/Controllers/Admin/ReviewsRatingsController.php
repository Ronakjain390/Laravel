<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Reviewsratings;
use Illuminate\Http\Request;
use Datatables;

class ReviewsRatingsController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return datatables()->of(Reviewsratings::select('*'))
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('admin/pages/reviewsratings.index');
    }

    public function create()
    {
        return view('admin/pages/reviewsratings.create');
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

            request()->image->move(public_path('assets/images/reviewrating/'), $file_name);
        }

        $reviewsrating = new Reviewsratings;
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
        return redirect()->route('reviewsratings.index')
            ->with('success', 'Review/Rating has been created successfully.');
    }

    public function show(Reviewsratings $reviewsrating)
    {
        return view('admin/pages/reviewsratings.show', compact('reviewsrating'));
    }

    public function edit(Reviewsratings $reviewsrating)
    {
        return view('admin/pages/reviewsratings.edit', compact('reviewsrating'));
    }

    public function update(Request $request, $id)
    {
        $reviewsrating = Reviewsratings::find($id);
        if ($request->action == 'status_toggle') {
            if ($reviewsrating->status == 0) {
                $reviewsrating->status = 1;
            } else {
                $reviewsrating->status = 0;
            }
            $reviewsrating->save();
            return redirect()->route('reviewsratings.index')
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
            return redirect()->route('reviewsratings.index')
                ->with('success', 'Review/Rating Has Been updated successfully');
        }
    }
    public function destroy(Request $request)
    {
        $com = Reviewsratings::where('id', $request->id)->delete();
        return response()->json(['success' => true, 'success' => 'Article deleted successfully']);
    }
}
