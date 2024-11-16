<?php

namespace App\Http\Controllers\Web\Panel;

use App\Models\Panel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PanelController extends Controller
{

// Retrieve all panels
public function index(Request $request)
{
    $query = Panel::query();

    // Filter by panel_name
    if ($request->input('panel_name')) {
        $query->where('panel_name', $request->input('panel_name'));
    }

    // Filter by section_id
    if ($request->input('section_id')) {
        $query->where('section_id', $request->input('section_id'));
    }

    // Filter by status
    if ($request->input('status')) {
        $query->where('status', $request->input('status'));
    }

    // Get filtered panels
    $panels = $query->get();

    return response()->json([
        'data' => $panels,
        'message' => 'Success',
        'status_code' => 200
    ]);
}


// Store a new panel
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'panel_name' => 'required|string|max:255',
        'section_id' => 'required|exists:section,id',
        'status' => 'required|in:active,pause,terminated',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
            'status_code' => 422,
        ], 422);
    }

    $existingPanel = Panel::where('panel_name', $request->panel_name)->first();

    if ($existingPanel) {
        // Panel with the same name already exists
        return response()->json([
            'data' => $existingPanel,
            'message' => 'Panel already exists.',
            'status_code' => 409
        ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
    }

    $Panel = Panel::create($request->all());

    return response()->json([
        'data' => $Panel,
        'message' => 'Panel Created',
        'status_code' => 201
    ], 201);
}

// Retrieve a panel by ID
public function show($id)
{
    $Panel = Panel::find($id);

    if (!$Panel) {
        return response()->json([
            'data' => null,
            'message' => 'Panel not found',
            'status_code' => 400,
        ], 400);
    }

    return response()->json([
        'data' => $Panel,
        'message' => 'Success',
        'status_code' => 200
    ]);
}

// Update a panel
public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'panel_name' => 'required|string|max:255',
        'section_id' => 'required|exists:section,id',
        'status' => 'required|in:active,pause,terminated',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
            'status_code' => 422,
        ], 422);
    }

    $Panel = Panel::find($id);

    if (!$Panel) {
        return response()->json([
            'data' => null,
            'message' => 'Panel not found',
            'status_code' => 400,
        ], 400);
    }

    $Panel->update($request->all());

    return response()->json([
        'data' => $Panel,
        'message' => 'Panel Updated',
        'status_code' => 200
    ]);
}



public function delete($id)
{
    // Get the panel with the given ID.
    $Panel = Panel::find($id);

    // Delete the panel.
    // $Panel->delete();

    // Return a 404 response if the panel doesn't exist
    if ($Panel->status == 'terminated') {
        return response()->json([
            'data' => null,
            'message' => 'Panel Already deleted.',
            'status_code' => 400,
        ], 400);
    }

    // Fill in the panel's data.
    $Panel->status = 'terminated';

    // Save the panel.
    $Panel->save();

    // Update the updated_at timestamp
    $Panel->touch();

    // Return a success message.
    return response()->json([
        'data' => null,
        'message' => 'Panel Deleted',
        'status_code' => 200
    ]);
}


// Destroy a panel
public function destroy($id)
{
   // Get the Panel with the given ID.
   $Panel = Panel::find($id);

   // Return a 404 response if the Panel doesn't exist
   if (!$Panel) {
       return response()->json([
           'data' => null,
           'message' => 'Panel Already Destroyed.',
           'status_code' => 400,
       ], 400);
   }

   // Check if the Panel status is "terminated"
   if ($Panel->status !== 'terminated') {
       return response()->json([
           'data' => null,
           'message' => 'Please terminate this Panel first.',
           'status_code' => 400,
       ], 400);
   }

   // Delete the Panel.
   $Panel->delete();

   // Return a success message.
   return response()->json([
       'data' => null,
       'message' => 'Panel Destroyed',
       'status_code' => 200
   ]);
}

}
