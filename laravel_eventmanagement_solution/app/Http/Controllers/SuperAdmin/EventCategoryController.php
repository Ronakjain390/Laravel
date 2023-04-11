<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class EventCategoryController extends Controller
{
    public function index()
    {
        $eventCategoryData = EventCategory::all();
        return view('SuperAdmin/Pages/EventCategory/Index', compact('eventCategoryData'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/EventCategory/Create');
    }

    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'category_name' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        if ($request->image) {
            $image = time() . '.' . request()->file('image')->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images/'), $image);
        } else {
            $image = " ";
        }

        $eventCategory = new EventCategory();
        $eventCategory->category_name = $request->category_name;
        $eventCategory->image = $image;
        if ($request->description) {
            $eventCategory->description = $request->description;
        }

        if ($request->status == 'on') {
            $eventCategory->status = 1;
        } else {
            $eventCategory->status = 0;
        }
        $eventCategory->save();
        return redirect()->route('EventCategory')
            ->with('success', 'Event Category has been created successfully.');
    }

    public function show($id)
    {
        $eventCategory = EventCategory::find($id);

        return view('SuperAdmin/Pages/EventCategory/Show', compact('eventCategory'));
    }

    public function edit($id)
    {
        $eventCategoryEdit = EventCategory::find($id);
        return view('SuperAdmin/Pages/EventCategory/Edit', compact('eventCategoryEdit'));
    }

    public function update(Request $request, $id)
    {
        $eventCategoryUpdate = EventCategory::find($id);
        if ($request->status == 'on') {
            $eventCategoryUpdate->status = '1';
        } else {
            $eventCategoryUpdate->status = '0';
        }

        $image = $request->hidden_Image;

        if ($request->image != '') {
            $image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images'), $image);
        }

        $eventCategoryUpdate->category_name = $request->category_name;
        $eventCategoryUpdate->description = $request->description;
        $eventCategoryUpdate->image = $image;
        $eventCategoryUpdate->save();

        return redirect()->route('EventCategory')
            ->with('success', 'Category has been updated successfully');
    }


    public function delete($id)
    {
        $eventCategoryDelete = EventCategory::where('id', $id)->delete();
        return redirect()->route('EventCategory')->with('error', 'Category deleted successfully');
    }
}
