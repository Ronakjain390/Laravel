<?php

namespace App\Http\Controllers\V1\PanelColumns;

use App\Models\PanelColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PanelColumnsController extends Controller
{
 
    public function index(Request $request)
    {
        try {
            // Apply filters
            $query = DB::table('panel_columns')
                ->select('id', 'panel_column_default_name', 'panel_column_display_name', 'default', 'user_id', 'panel_id', 'section_id', 'feature_id', 'status')
                ->where(function ($query) use ($request) {
                    if ($request->has('user_id')) {
                        $query->where('user_id', $request->user_id);
                    }
                    if ($request->has('panel_id')) {
                        $query->where('panel_id', $request->panel_id);
                    }
                    if ($request->has('section_id')) {
                        $query->where('section_id', $request->section_id);
                    }
                    if ($request->has('feature_id')) {
                        $query->where('feature_id', $request->feature_id);
                    }
                    if ($request->has('default')) {
                        $query->where('default', $request->default);
                    }
                    if ($request->has('status')) {
                        $query->where('status', $request->status);
                    }
                });

            // Execute the query
            $panelColumns = $query->get();

            // Return the response
            if ($panelColumns->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'data' => $panelColumns,
                    'message' => 'No panel columns found.',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $panelColumns,
                'message' => 'Panel columns found.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function show($id)
    {
        // Retrieve the panel column record from the database
        $panelColumn = DB::table('panel_columns')
            ->select('id', 'panel_column_default_name', 'panel_column_display_name', 'user_id', 'panel_id', 'section_id', 'feature_id', 'status')
            ->where('id', $id)
            ->first();

        // Check if the panel column exists
        if (!$panelColumn) {
            return response()->json([
                'success' => false,
                'data' => $panelColumn,
                'message' => 'Panel column not found.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $panelColumn,
            'message' => 'Panel column retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        //  Set the user_id from the authenticated user
        // $request->merge(['user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id]);
        // dd($request);
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'panel_column_default_name' => 'nullable|string|max:255',
            'panel_column_display_name' => 'nullable|string|max:255',
            // 'default' => 'required|in:0,1',
            // 'fixed' => 'required|in:0,1',
            'user_id' => 'required|exists:users,id',
            'feature_id' => 'required|exists:features,id',
            'panel_id' => 'required|exists:panel,id',
            'section_id' => 'required|exists:section,id',
            'status' => 'required|in:active,pause,terminated',
        ]);
        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 400);
        }
        // Check if the panel column already exists
        $existingPanelColumn = DB::table('panel_columns')
            ->where('panel_column_default_name', $request->panel_column_default_name)
            ->where('panel_column_display_name', $request->panel_column_display_name)
            ->where('panel_id', $request->panel_id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existingPanelColumn) {
            // Check if the feature_id is the same as the request or different
            if ($existingPanelColumn->feature_id == $request->feature_id) {
                // Panel column with the same name, feature_id, panel_id, and user_id exists
                return response()->json([
                    'success' => false,
                    'message' => 'Panel column already exists.',
                ], 409);
            }
        }

        $panelColumn = PanelColumn::create($request->all());
        // dd($panelColumn);
        return response()->json([
            'success' => true,
            'message' => 'Panel column created successfully.',
            'data' => $panelColumn,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // Set the user_id from the authenticated user
        // dd($request, $id);
        $request->merge(['user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id]);

        // Validate the input data
        $validator = Validator::make($request->all(), [
            // 'panel_column_default_name' => 'required|string|max:255',
            // 'panel_column_display_name' => 'required|string|max:255',
            // 'default' => 'required|in:0,1',
            // 'fixed' => 'required|in:0,1',
            'user_id' => 'required|exists:users,id',
            'feature_id' => 'required|exists:features,id',
            'panel_id' => 'required|exists:panel,id',
            'section_id' => 'required|exists:section,id',
            'status' => 'required|in:active,pause,terminated',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Check if the panel column exists
        $existingPanelColumn = DB::table('panel_columns')
            ->where('id', $id)
            ->first();

        if (!$existingPanelColumn) {
            return response()->json([
                'success' => false,
                'message' => 'Panel column not found.',
                'data' => null,
            ], 400);
        }

        // Update the panel column
        DB::table('panel_columns')
            ->where('id', $id)
            ->update([
                'panel_column_default_name' => $request->panel_column_default_name,
                'panel_column_display_name' => $request->panel_column_display_name,
                // 'default' => $request->default,
                // 'fixed' => $request->fixed,
                'user_id' => $request->user_id,
                'feature_id' => $request->feature_id,
                'panel_id' => $request->panel_id,
                'section_id' => $request->section_id,
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        // Get the updated panel column
        $updatedPanelColumn = DB::table('panel_columns')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Panel column updated successfully.',
            'data' => $updatedPanelColumn,
            'status_code' => 200,
        ]);
    }

    public function delete($id)
    {
        // Get the panel column with the given ID
        $panelColumn = PanelColumn::find($id);

        // Return a 404 response if the panel column doesn't exist
        if (!$panelColumn) {
            return response()->json([
                'data' => null,
                'message' => 'Panel column not found.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the panel column is already terminated
        if ($panelColumn->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Panel column already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Update the status of the panel column to "terminated"
        $panelColumn->status = 'terminated';

        // Save the updated panel column
        $panelColumn->save();

        // Update the updated_at timestamp
        $panelColumn->touch();

        // Return a success message
        return response()->json([
            'data' => null,
            'message' => 'Panel column deleted successfully.',
            'status_code' => 200,
        ]);
    }
    public function destroy(string $id)
    {
        // Get the panel column with the given ID
        $panelColumn = PanelColumn::find($id);

        // Return a 404 response if the panel column doesn't exist
        if (!$panelColumn) {
            return response()->json([
                'data' => null,
                'message' => 'Panel column not found.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the panel column status is "terminated"
        if ($panelColumn->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this panel column first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the panel column
        $panelColumn->delete();

        // Return a success message
        return response()->json([
            'data' => null,
            'message' => 'Panel column destroyed successfully.',
            'status_code' => 200,
        ]);
    }
}
