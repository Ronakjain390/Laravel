<?php

namespace App\Http\Controllers\Web\AdditionalFeatures;

use App\Models\AdditionalFeature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AdditionalFeaturesController extends Controller
{
    //

    public function index(Request $request)
    {
        $query = AdditionalFeature::query();

        // Filter by additional_feature_name
        if ($request->input('additional_feature_name')) {
            $query->where('additional_feature_name', $request->input('additional_feature_name'));
        }

        // Filter by section_id
        if ($request->input('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }

        // Filter by panel_id
        if ($request->input('panel_id')) {
            $query->where('panel_id', $request->input('panel_id'));
        }

        // Filter by feature_id
        if ($request->input('feature_id')) {
            $query->where('feature_id', $request->input('feature_id'));
        }

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Get filtered additional_feature_name
        $AdditionalFeatures = $query->get();

        return response()->json([
            'data' => $AdditionalFeatures,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }


    // Store a new AdditionalFeature
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'additional_feature_name' => 'required|string|max:255',
            'section_id' => 'required|exists:section,id',
            'panel_id' => 'required|exists:panel,id',
            'feature_id' => 'required|exists:features,id',
            'status' => 'required|in:active,pause,terminated',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $existingAdditionalFeature = AdditionalFeature::where('additional_feature_name', $request->additional_feature_name)->first();

        if ($existingAdditionalFeature) {
            // AdditionalFeature with the same name already exists
            return response()->json([
                'data' => $existingAdditionalFeature,
                'message' => 'AdditionalFeature already exists.',
                'status_code' => 409
            ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
        }

        $AdditionalFeature = AdditionalFeature::create($request->all());

        return response()->json([
            'data' => $AdditionalFeature,
            'message' => 'AdditionalFeature Created',
            'status_code' => 201
        ], 201);
    }

    // Retrieve a AdditionalFeature by ID
    public function show($id)
    {
        $AdditionalFeature = AdditionalFeature::find($id);

        if (!$AdditionalFeature) {
            return response()->json([
                'data' => null,
                'message' => 'AdditionalFeature not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $AdditionalFeature,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    // Update a Panel
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'additional_feature_name' => 'required|string|max:255',
            'section_id' => 'required|exists:section,id',
            'panel_id' => 'required|exists:panel,id',
            'feature_id' => 'required|exists:features,id',
            'status' => 'required|in:active,pause,terminated',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $AdditionalFeature = AdditionalFeature::find($id);

        if (!$AdditionalFeature) {
            return response()->json([
                'data' => null,
                'message' => 'AdditionalFeature not found',
                'status_code' => 400,
            ], 400);
        }

        $AdditionalFeature->update($request->all());

        // Update the updated_at timestamp
        $AdditionalFeature->touch();

        return response()->json([
            'data' => $AdditionalFeature,
            'message' => 'AdditionalFeature Updated',
            'status_code' => 200
        ]);
    }



    public function delete($id)
    {
        // Get the AdditionalFeature with the given ID.
        $AdditionalFeature = AdditionalFeature::find($id);

        // Delete the AdditionalFeature.
        // $AdditionalFeature->delete();

        // Return a 404 response if the AdditionalFeature doesn't exist
        if ($AdditionalFeature->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'AdditionalFeature Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the Panel's data.
        $AdditionalFeature->status = 'terminated';

        // Save the AdditionalFeature.
        $AdditionalFeature->save();

        // Update the updated_at timestamp
        $AdditionalFeature->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'AdditionalFeature Deleted',
            'status_code' => 200
        ]);
    }


    // Destroy a AdditionalFeature
    public function destroy($id)
    {
        // Get the AdditionalFeature with the given ID.
        $AdditionalFeature = AdditionalFeature::find($id);

        // Return a 404 response if the AdditionalFeature doesn't exist
        if (!$AdditionalFeature) {
            return response()->json([
                'data' => null,
                'message' => 'AdditionalFeature Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the AdditionalFeature status is "terminated"
        if ($AdditionalFeature->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this AdditionalFeature first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the AdditionalFeature.
        $AdditionalFeature->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'AdditionalFeature Destroyed',
            'status_code' => 200
        ]);
    }
}
