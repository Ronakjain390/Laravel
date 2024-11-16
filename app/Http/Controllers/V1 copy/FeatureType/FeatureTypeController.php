<?php

namespace App\Http\Controllers\Web\FeatureType;

use App\Models\FeatureType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FeatureTypeController extends Controller
{
    //

    public function index(Request $request)
    {
        $query = FeatureType::query();

        // Filter by feature_type_name
        if ($request->input('feature_type_name')) {
            $query->where('feature_type_name', $request->input('feature_type_name'));
        }

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Get filtered FeatureTypes
        $featureTypes = $query->get();

        return response()->json([
            'data' => $featureTypes,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }


    // Store a new FeatureType
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feature_type_name' => 'required|string|max:255',
            'status' => 'required|in:active,pause,terminated',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $existingFeatureType = FeatureType::where('feature_type_name', $request->feature_type_name)->first();

        if ($existingFeatureType) {
            // FeatureType with the same name already exists
            return response()->json([
                'data' => $existingFeatureType,
                'message' => 'FeatureType already exists.',
                'status_code' => 409
            ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
        }

        $FeatureType = FeatureType::create($request->all());

        return response()->json([
            'data' => $FeatureType,
            'message' => 'FeatureType Created',
            'status_code' => 201
        ], 201);
    }

    // Retrieve a FeatureType by ID
    public function show($id)
    {
        $FeatureType = FeatureType::find($id);

        if (!$FeatureType) {
            return response()->json([
                'data' => null,
                'message' => 'FeatureType not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $FeatureType,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    // Update a FeatureType
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'feature_type_name' => 'required|string|max:255',
            'status' => 'required|in:active,pause,terminated',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $FeatureType = FeatureType::find($id);

        if (!$FeatureType) {
            return response()->json([
                'data' => null,
                'message' => 'FeatureType not found',
                'status_code' => 400,
            ], 400);
        }

        $FeatureType->update($request->all());

        // Update the updated_at timestamp
        $FeatureType->touch();

        return response()->json([
            'data' => $FeatureType,
            'message' => 'FeatureType Updated',
            'status_code' => 200
        ]);
    }



    public function delete($id)
    {
        // Get the FeatureType with the given ID.
        $FeatureType = FeatureType::find($id);

        // Delete the FeatureType.
        // $FeatureType->delete();

        // Return a 404 response if the FeatureType doesn't exist
        if ($FeatureType->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'FeatureType Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the FeatureType's data.
        $FeatureType->status = 'terminated';

        // Save the FeatureType.
        $FeatureType->save();

        // Update the updated_at timestamp
        $FeatureType->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'FeatureType Deleted',
            'status_code' => 200
        ]);
    }


    // Destroy a FeatureType
    public function destroy($id)
    {
        // Get the FeatureType with the given ID.
        $FeatureType = FeatureType::find($id);

        // Return a 404 response if the FeatureType doesn't exist
        if (!$FeatureType) {
            return response()->json([
                'data' => null,
                'message' => 'FeatureType Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the FeatureType status is "terminated"
        if ($FeatureType->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this FeatureType first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the FeatureType.
        $FeatureType->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'FeatureType Destroyed',
            'status_code' => 200
        ]);
    }
}
