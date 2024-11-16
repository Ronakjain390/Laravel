<?php

namespace App\Http\Controllers\V1\Feature;

use App\Models\Feature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeatureController extends Controller
{
    // Retrieve all features
    public function index(Request $request)
    {
        $query = Feature::query()->with('template');


        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name',Auth::getDefaultDriver())->exists()) {
            $query->with(['usageRecords','topupUsageRecords']);
        }

        // Filter by feature_name
        if ($request->input('feature_name')) {
            $query->where('feature_name', $request->input('feature_name'));
        }

        // Filter by feature_type_id
        if ($request->input('feature_type_id')) {
            $query->where('feature_type_id', $request->input('feature_type_id'));
        }

        // Filter by panel_id
        if ($request->input('panel_id')) {
            $query->where('panel_id', $request->input('panel_id'));
        }

         // Filter by section_id
         if ($request->input('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }

         if ($request->input('template_id')) {
            $query->where('template_id', $request->input('template_id'));
        }

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Get filtered features
        $features = $query->get();

        return response()->json([
            'data' => $features,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    // Store a new feature
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'feature_type_id' => 'nullable|exists:feature_type,id',
            'panel_id' => 'nullable|exists:panel,id',
            'section_id' => 'nullable|exists:section,id',
            'feature_name' => 'required|string|max:255',
            'template_id' => 'nullable|exists:templates,id',
            'status' => 'nullable|in:active,pause,terminated',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        // $existingFeature = Feature::where('feature_name', $request->feature_name)->where('panel_id',$request->panel_id)->first();

        // if ($existingFeature) {
        //     // Feature with the same name already exists
        //     return response()->json([
        //         'data' => $existingFeature,
        //         'message' => 'Feature already exists.',
        //         'status_code' => 409
        //     ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
        // }

        $feature = Feature::create($request->all());

        return response()->json([
            'data' => $feature,
            'message' => 'Feature Created',
            'status_code' => 201
        ], 201);
    }

    // Retrieve a feature by ID
    public function show($id)
    {
        $feature = Feature::with('template')->find($id);

        if (!$feature) {
            return response()->json([
                'data' => null,
                'message' => 'Feature not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $feature,
            'message' => 'Success',
            'status_code' => 200
        ]);

    }

    // Update a feature
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'feature_type_id' => 'nullable|exists:feature_type,id',
            'panel_id' => 'nullable|exists:panel,id',
            'section_id' => 'nullable|exists:section,id',
            'feature_name' => 'nullable|string|max:255',
            'template_id' => 'nullable|exists:templates,id',
            'status' => 'nullable|in:active,pause,terminated',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $feature = Feature::find($id);

        if (!$feature) {
            return response()->json([
                'data' => null,
                'message' => 'Feature not found',
                'status_code' => 400,
            ], 400);
        }

        $feature->update($request->all());

        // Update the updated_at timestamp
        $feature->touch();

        return response()->json([
            'data' => $feature,
            'message' => 'Feature Updated',
            'status_code' => 200
        ]);
    }

    // Delete a feature
    public function delete($id)
    {
        // Get the Feature with the given ID.
        $Feature = Feature::find($id);

        // Delete the Feature.
        // $Feature->delete();

        // Return a 404 response if the Feature doesn't exist
        if ($Feature->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Feature Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the Panel's data.
        $Feature->status = 'terminated';

        // Save the Feature.
        $Feature->save();

        // Update the updated_at timestamp
        $Feature->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Feature Deleted',
            'status_code' => 200
        ]);
    }


    // Destroy a Feature
    public function destroy($id)
    {
        // Get the Feature with the given ID.
        $Feature = Feature::find($id);

        // Return a 404 response if the Feature doesn't exist
        if (!$Feature) {
            return response()->json([
                'data' => null,
                'message' => 'Feature Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the Feature status is "terminated"
        if ($Feature->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this Feature first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the Feature.
        $Feature->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Feature Destroyed',
            'status_code' => 200
        ]);
    }
}
