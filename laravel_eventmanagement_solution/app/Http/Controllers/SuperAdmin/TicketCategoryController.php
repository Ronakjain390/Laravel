<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class TicketCategoryController extends Controller
{
    public function index()
    {
        $ticketCategoryData = TicketCategory::all();
        return view('SuperAdmin/Pages/TicketCategory/Index', compact('ticketCategoryData'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/TicketCategory/Create');
    }

    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }
        if ($request->image) {
            $image = time() . '.' . request()->file('image')->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images'), $image);
        } else {
            $image = "";
        }

        $ticketCategory = new TicketCategory;
        $ticketCategory->name = $request->name;
        if ($request->description) {
            $ticketCategory->description = $request->description;
        }
        $ticketCategory->image = $image;

        if ($request->status == 'on') {
            $ticketCategory->status = 1;
        } else {
            $ticketCategory->status = 0;
        }
        $ticketCategory->save();
        return redirect()->route('TicketCategory')
            ->with('success', 'Event Category has been created successfully.');
    }

    public function show($id)
    {
        $ticketCategory = TicketCategory::find($id);

        return view('SuperAdmin/Pages/TicketCategory/Show', compact('ticketCategory'));
    }

    public function edit($id)
    {
        $ticketCategoryEdit = TicketCategory::find($id);
        return view('SuperAdmin/Pages/TicketCategory/Edit', compact('ticketCategoryEdit'));
    }

    public function update(Request $request, $id)
    {
        $ticketCategoryUpdate = TicketCategory::find($id);

        if ($request->status == 'on') {
            $ticketCategoryUpdate->status = '1';
        } else {
            $ticketCategoryUpdate->status = '0';
        }

        $image = $request->hidden_Image;

        if ($request->image != '') {
            $image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images'), $image);
        }

        $ticketCategoryUpdate->name = $request->name;
        $ticketCategoryUpdate->image = $image;
        $ticketCategoryUpdate->description = $request->description;
        $ticketCategoryUpdate->save();

        return redirect()->route('TicketCategory')
            ->with('success', 'Category has been updated successfully');
    }
    function delete($id)
    {
        TicketCategory::find($id)->delete();

        return redirect()->route('TicketCategory')->with('error', 'Ticket Category has been created successfully.');
    }
}
