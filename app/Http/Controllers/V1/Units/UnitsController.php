<?php

namespace App\Http\Controllers\V1\Units;

use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UnitsController extends Controller
{
    /**
     * Display a listing of the units.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($panel_type)
    {
        $user_id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id; // Get the authenticated user's ID
        $units = Units::where('user_id', $user_id)->where('panel_type', $panel_type)->get();
        return response()->json($units, Response::HTTP_OK);
    }
    /**
     * Store a newly created unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        // try {
            // Validate the request data
            // $request->validate([
            //     'unit' => 'nullable|string|max:255|unique:units',
            //     'short_name' => 'nullable|string|max:255|unique:units',
            //     'status' => 'required|in:active,pause,terminated',
            //     'panel_name' => 'required|string', // Validate panel_name
            //     'is_default' => 'required|boolean', // Validate is_default
            // ]);

            // Get the authenticated user's ID
            $user_id = Auth::getDefaultDriver() == 'team-user'
                ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
                : Auth::guard(Auth::getDefaultDriver())->user()->id;

            // // Merge request data with user ID
            // $unitData = array_merge($request->all(), ['user_id' => $user_id]);

            // Create the unit
            // $unit = Units::create($unitData);

            $unit = new Units([
                'unit' => $request->unit,
                'short_name' => $request->short_name,
                'status' => $request->status,
                'panel_type' => $request->panel_type,
                'is_default' => $request->is_default,
                'user_id' => $user_id
            ]);
            $unit->save();

            // Return a JSON response with the created unit
            return response()->json([
                'unit' => $unit,
                'status_code' => 200,
                'message' => 'Unit Created Successfully'
            ], 200);

        // } catch (ValidationException $e) {
        //     // Return a JSON response with validation errors
        //     return response()->json([
        //         'error' => $e->errors(),
        //         'status_code' => Response::HTTP_BAD_REQUEST
        //     ], Response::HTTP_BAD_REQUEST);
        // }
    }

    /**
     * Display the specified unit.
     *
     * @param  \App\Models\Units  $unit
     * @return \Illuminate\Http\Response
     */
    public function show($unitId)
    {
        $unit = Units::find($unitId);

        if (!$unit) {
            return response()->json([
                'data' => null,
                'message' => 'unit not found',
                'status_code' => 200,
            ], 200);
        }

        return response()->json([
            'data' => $unit,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    /**
     * Update the specified unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Units  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$unitId)
    {
        $unit = Units::find($unitId);

        if (!$unit) {
            return response()->json([
                'data' => null,
                'message' => 'unit not found',
                'status_code' => 200,
            ], 200);
        }

        try {
            $request->validate([
                'unit' => 'required|string|max:255|unique:units,unit,' . $unit->id,
                'status' => 'required|in:active,pause,terminated',
            ]);

            $user_id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id; // Get the authenticated user's ID

            $unitData = array_merge($request->all(), ['user_id' => $user_id]);

            $unit->update($unitData);
            return response()->json([
                'data' => $unit,
                'message' => 'Success',
                'status_code' => 200
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the unit.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified unit from storage.
     *
     * @param  \App\Models\Units  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $unitId)
    {
        try {
            // Find the Challan by ID
            $challan = Units::findOrFail($unitId);

            $challan->delete();

            // Return a response indicating success
            return response()->json([
                'message' => 'Unit permanently deleted.',
                'status_code' => Response::HTTP_OK
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'unit Not Found.',
                'status_code' => Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    public function showHideUnits(Request $request)
    {
        $request->validate([
            'selectedUnits' => 'required|array',
            'unselectedUnits' => 'required|array',
        ]);

        // Update selected units
        Units::whereIn('id', $request->selectedUnits)
            ->update([
                'status' => 'active',
                'is_default' => 1
            ]);

        // Update unselected units
        Units::whereIn('id', $request->unselectedUnits)
            ->update([
                'is_default' => 0
            ]);

        return Units::all(); // Or any other response you want to return
    }

}
