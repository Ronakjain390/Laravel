<?php

namespace App\Http\Controllers\Web\PanelSeriesNumber;

use Illuminate\Http\Request;
use App\Models\PanelSeriesNumber;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PanelSeriesNumberController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = PanelSeriesNumber::query();

        // Apply column filters if provided
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name',Auth::getDefaultDriver())->exists()) {
            $query->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
        } elseif ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('panel_id')) {
            $query->where('panel_id', $request->panel_id);
        }

        if ($request->has('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->has('assigned_to_id')) {
            $query->where('assigned_to_id', $request->assigned_to_id);
        }
        if ($request->has('assigned_to_name')) {
            $query->where('assigned_to_name', $request->assigned_to_name);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('valid_from')) {
            $query->where('valid_from', $request->valid_from);
        }
        if ($request->has('valid_till')) {
            $query->where('valid_till', $request->valid_till);
        }
        if ($request->has('default')) {
            $query->where('default', $request->default);
        }

        // Retrieve filtered panel series numbers
        $panelSeriesNumbers = $query->get();

        return response()->json([
            'success' => true,
            'data' => $panelSeriesNumbers,
            'message' => 'Panel series numbers retrieved successfully.',
        ]);
    }

    public function show($id)
    {
        $panelSeriesNumber = PanelSeriesNumber::find($id);

        if (!$panelSeriesNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Panel series number not found.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $panelSeriesNumber,
            'message' => 'Panel series number retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'series_number' => 'required|string|max:255',
            'panel_id' => 'required|exists:panel,id',
            'section_id' => 'required|exists:section,id',
            'assigned_to_r_id' => 'nullable|exists:receivers,id',
            'assigned_to_b_id' => 'nullable|exists:buyer,id',
            'assigned_to_name' => 'nullable|string|max:255',
            'status' => 'required|in:active,pause,terminated',
            'valid_from' => 'required|date',
            'valid_till' => 'required|date',
            'default' => 'required|boolean',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 400);
        }


        if ($request->assigned_to_r_id != null) {
            $assigned_to_id = $request->assigned_to_r_id;
        } else if ($request->assigned_to_b_id != null) {
            $assigned_to_id = $request->assigned_to_b_id;
        } else {
            $assigned_to_id = null;
        }


        // Check if there is any existing "panelseriesnumber" with default status for the given "panel_id" and "user_id"
        $existingDefaultSeriesNumber = PanelSeriesNumber::where('panel_id', $request->panel_id)
            ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->where('default', 1)
            ->first();

        if ($request->default == 1) {
            if ($existingDefaultSeriesNumber) {
                // Update the existing default series number to '0' for 'default' field
                $existingDefaultSeriesNumber->update([
                    'default' => 0,
                ]);
            }
        } else {
            if (!$existingDefaultSeriesNumber) {
                // If request data has default set to 0 or not provided, update it to 1
                $request->merge(['default' => 1]);
            }
        }


        // Check if the series number already exists
        $existingSeriesNumber = PanelSeriesNumber::where('series_number', $request->series_number)
            ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->where('panel_id', $request->panel_id)
            ->first();

        if ($existingSeriesNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Series number already exists.',
            ], 409);
        }

        // Check if the Receiver already assigned
        $existingReceiver = PanelSeriesNumber::where('assigned_to_id', $assigned_to_id)
            ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->where('panel_id', $request->panel_id)
            ->first();

        if ($existingReceiver) {
            $existingReceiver->update([
                'assigned_to_id' => null,
            ]);
        }

        // Create a new panel series number
        $panelSeriesNumber = PanelSeriesNumber::create([
            'series_number' => $request->series_number,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'panel_id' => $request->panel_id,
            'section_id' => $request->section_id,
            'assigned_to_id' => $assigned_to_id,
            'assigned_to_name' => $request->assigned_to_name,
            'status' => $request->status,
            'valid_from' => $request->valid_from,
            'valid_till' => $request->valid_till,
            'default' => $request->default,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Panel series number created successfully.',
            'data' => $panelSeriesNumber,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'series_number' => 'required|string|max:255',
            'panel_id' => 'required|exists:panel,id',
            'section_id' => 'required|exists:section,id',
            'assigned_to_r_id' => 'nullable|exists:receivers,id',
            'assigned_to_b_id' => 'nullable|exists:buyer,id',
            'assigned_to_name' => 'nullable|string|max:255',
            'status' => 'required|in:active,pause,terminated',
            'valid_from' => 'required|date',
            'valid_till' => 'required|date',
            'default' => 'required|boolean',
        ]);

        if ($request->assigned_to_r_id != null) {
            $assigned_to_id = $request->assigned_to_r_id;
        } else if ($request->assigned_to_b_id != null) {
            $assigned_to_id = $request->assigned_to_b_id;
        } else {
            $assigned_to_id = null;
        }

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Check if the panel series number exists
        $panelSeriesNumber = PanelSeriesNumber::find($id);

        if (!$panelSeriesNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Panel series number not found.',
            ], 400);
        }

        // Check if there is any existing "panelseriesnumber" with default status for the given "panel_id" and "user_id"
        $existingDefaultSeriesNumber = PanelSeriesNumber::where('panel_id', $request->panel_id)
            ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->where('default', 1)
            ->first();

        if ($request->default == 1) {
            if ($existingDefaultSeriesNumber) {
                // Update the existing default series number to '0' for 'default' field
                $existingDefaultSeriesNumber->update([
                    'default' => 0,
                ]);
            }
        } else {
            if (!$existingDefaultSeriesNumber) {
                // If request data has default set to 0 or not provided, update it to 1
                $request->merge(['default' => 1]);
            }
        }


        // Check if the series number already exists
        $existingSeriesNumber = PanelSeriesNumber::where('series_number', $request->series_number)
            ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->where('panel_id', $request->panel_id)
            ->first();

        if ($existingSeriesNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Series number already exists.',
            ], 409);
        }

        // Check if the Receiver already assigned
        $existingReceiver = PanelSeriesNumber::where('assigned_to_id', $assigned_to_id)
            ->where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
            ->where('panel_id', $request->panel_id)
            ->first();

        if ($existingReceiver) {
            $existingReceiver->update([
                'assigned_to_id' => null,
            ]);
        }

        // Update the panel series number
        $panelSeriesNumber->update([
            'series_number' => $request->series_number,
            // 'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'panel_id' => $request->panel_id,
            'section_id' => $request->section_id,
            'assigned_to_id' => $assigned_to_id,
            'assigned_to_name' => $request->assigned_to_name,
            'status' => $request->status,
            'valid_from' => $request->valid_from,
            'valid_till' => $request->valid_till,
            'default' => $request->default,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Panel series number updated successfully.',
            'data' => $panelSeriesNumber,
        ]);
    }

    public function delete($id)
    {
        // Get the panel series number with the given ID.
        $panelSeriesNumber = PanelSeriesNumber::find($id);

        // Return a 404 response if the panel series number doesn't exist
        if (!$panelSeriesNumber) {
            return response()->json([
                'data' => null,
                'message' => 'Panel series number not found.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the panel series number is already terminated
        if ($panelSeriesNumber->status === 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Panel series number already terminated.',
                'status_code' => 400,
            ], 400);
        }

        // Set the status of the panel series number to "terminated"
        $panelSeriesNumber->status = 'terminated';

        // Save the panel series number
        $panelSeriesNumber->save();

        // Update the updated_at timestamp
        $panelSeriesNumber->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Panel series number deleted.',
            'status_code' => 200
        ]);
    }
    public function destroy($id)
    {
        // Get the panel series number with the given ID.
        $panelSeriesNumber = PanelSeriesNumber::find($id);

        // Return a 404 response if the panel series number doesn't exist
        if (!$panelSeriesNumber) {
            return response()->json([
                'data' => null,
                'message' => 'Panel series number not found.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the panel series number status is "terminated"
        if ($panelSeriesNumber->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this panel series number first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the panel series number.
        $panelSeriesNumber->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Panel series number destroyed.',
            'status_code' => 200
        ]);
    }
}
