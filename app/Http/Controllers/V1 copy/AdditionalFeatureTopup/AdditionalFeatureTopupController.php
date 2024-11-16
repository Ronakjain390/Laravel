<?php

namespace App\Http\Controllers\Web\AdditionalFeatureTopup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdditionalFeatureTopup;
use Illuminate\Support\Facades\Validator;

class AdditionalFeatureTopupController extends Controller
{
    //

    public function index(Request $request)
    {
        $AdditionalFeatureTopup = AdditionalFeatureTopup::query();

        // Filter by feature ID
        if ($request->has('feature_id')) {
            $AdditionalFeatureTopup->where('feature_id', $request->input('feature_id'));
        }

        // Filter by usage limit
        if ($request->has('usage_limit')) {
            $AdditionalFeatureTopup->where('usage_limit', $request->input('usage_limit'));
        }

        $AdditionalFeatureTopup = $AdditionalFeatureTopup->with('additionalFeature')->get();

        return response()->json([
            'data' => $AdditionalFeatureTopup,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function show($id)
    {
        $AdditionalFeatureTopup = AdditionalFeatureTopup::find($id)->with('additionalFeature')->first();

        if (!$AdditionalFeatureTopup) {
            return response()->json([
                'data' => null,
                'message' => 'Additional Feature Topup not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $AdditionalFeatureTopup,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'additional_feature_id' => 'required|integer',
            'price' => 'required|integer|min:1',
            'usage_limit' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $existingAdditionalFeatureTopup = AdditionalFeatureTopup::where([['additional_feature_id', $request->additional_feature_id]])->first();

        if ($existingAdditionalFeatureTopup) {
            // Plan with the same name already exists
            return response()->json([
                'data' => $existingAdditionalFeatureTopup,
                'message' => 'Additional Feature TopUp already added.',
                'status_code' => 409
            ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
        }

        $AdditionalFeatureTopup = AdditionalFeatureTopup::create($request->all());

        return response()->json([
            'data' => $AdditionalFeatureTopup,
            'message' => 'Additional Feature TopUp Created',
            'status_code' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'additional_feature_id' => 'required|integer',
            'price' => 'required|integer|min:1',
            'usage_limit' => 'required|integer|min:1',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $AdditionalFeatureTopupexists = AdditionalFeatureTopup::where([['id', '!=', $id], ['additional_feature_id', $request->additional_feature_id]])->exists();

        if ($AdditionalFeatureTopupexists) {
            return response()->json([
                'data' => null,
                'message' => 'Additional Feature TopUp already exists',
                'status_code' => 400,
            ], 400);
        }

        $AdditionalFeatureTopup = AdditionalFeatureTopup::find($id);

        if (!$AdditionalFeatureTopup) {
            return response()->json([
                'data' => null,
                'message' => 'Additional Feature TopUp not found',
                'status_code' => 400,
            ], 400);
        }

        $AdditionalFeatureTopup->update($request->all());

        return response()->json([
            'data' => $AdditionalFeatureTopup,
            'message' => 'Additional Feature TopUp Updated',
            'status_code' => 200
        ]);
    }

    public function delete($id)
    {
        // Get the Plan with the given ID.
        $AdditionalFeatureTopup = AdditionalFeatureTopup::find($id);

        // Delete the Plan.
        // $AdditionalFeatureTopup->delete();

        // Return a 404 response if the Plan doesn't exist
        if ($AdditionalFeatureTopup->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Additional Feature TopUp Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the Plan's data.
        $AdditionalFeatureTopup->status = 'terminated';

        // Save the Plan.
        $AdditionalFeatureTopup->save();

        // Update the updated_at timestamp
        $AdditionalFeatureTopup->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Additional Feature TopUp Deleted',
            'status_code' => 200
        ]);
    }


    // Destroy a Plan
    public function destroy($id)
    {
        // Get the Plan with the given ID.
        $AdditionalFeatureTopup = AdditionalFeatureTopup::find($id);

        // Return a 404 response if the Plan doesn't exist
        if (!$AdditionalFeatureTopup) {
            return response()->json([
                'data' => null,
                'message' => 'Additional Feature TopUp Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the Plan status is "terminated"
        if ($AdditionalFeatureTopup->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this Additional Feature TopUp first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the Plan.
        $AdditionalFeatureTopup->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Additional Feature TopUp Destroyed',
            'status_code' => 200
        ]);
    }
}
