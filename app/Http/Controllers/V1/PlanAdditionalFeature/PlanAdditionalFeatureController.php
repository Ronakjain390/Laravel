<?php

namespace App\Http\Controllers\V1\PlanAdditionalFeature;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlanAdditionalFeature;
use Illuminate\Support\Facades\Validator;

class PlanAdditionalFeatureController extends Controller
{
    //
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:plans,id',
            'additional_feature_id' => 'required|integer|exists:additional_features,id',
            'additional_feature_usage_limit' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $existingPlanAdditionalFeature = PlanAdditionalFeature::where([['plan_id',$request->plan_id],['additional_feature_id',$request->additional_feature_id]])->first();

        if ($existingPlanAdditionalFeature) {
            // Plan with the same name already exists
            return response()->json([
                'data' => $existingPlanAdditionalFeature,
                'message' => 'Plan Additional Feature already added.',
                'status_code' => 409
            ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
        }

        $PlanAdditionalFeature = PlanAdditionalFeature::create($request->all());

        return response()->json([
            'data' => $PlanAdditionalFeature,
            'message' => 'Plan Additional Feature Created',
            'status_code' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:plans,id',
            'additional_feature_id' => 'required|integer|exists:additional_features,id',
            'additional_feature_usage_limit' => 'required|integer|min:1',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $PlanAdditionalFeatureexists = PlanAdditionalFeature::where([['id','!=',$id],['plan_id',$request->plan_id],['additional_feature_id',$request->additional_feature_id]])->exists();

        if ($PlanAdditionalFeatureexists) {
            return response()->json([
                'data' => null,
                'message' => 'Plan Additional Feature already exists',
                'status_code' => 400,
            ], 400);
        }

        $PlanAdditionalFeature = PlanAdditionalFeature::find($id);

        if (!$PlanAdditionalFeature) {
            return response()->json([
                'data' => null,
                'message' => 'Plan Additional Feature not found',
                'status_code' => 400,
            ], 400);
        }

        $PlanAdditionalFeature->update($request->all());

        return response()->json([
            'data' => $PlanAdditionalFeature,
            'message' => 'Plan Additional Feature Updated',
            'status_code' => 200
        ]);
    }

    public function delete($id)
    {
        // Get the Plan with the given ID.
        $PlanAdditionalFeature = PlanAdditionalFeature::find($id);

        // Delete the Plan.
        // $PlanAdditionalFeature->delete();

        // Return a 404 response if the Plan doesn't exist
        if ($PlanAdditionalFeature->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Plan Additional Feature Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the Plan's data.
        $PlanAdditionalFeature->status = 'terminated';

        // Save the Plan.
        $PlanAdditionalFeature->save();

        // Update the updated_at timestamp
        $PlanAdditionalFeature->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Plan Additional Feature Deleted',
            'status_code' => 200
        ]);
    }


    // Destroy a Plan
    public function destroy($id)
    {
        // Get the Plan with the given ID.
        $PlanAdditionalFeature = PlanAdditionalFeature::find($id);

        // Return a 404 response if the Plan doesn't exist
        if (!$PlanAdditionalFeature) {
            return response()->json([
                'data' => null,
                'message' => 'Plan Additional Feature Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the Plan status is "terminated"
        if ($PlanAdditionalFeature->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this Plan Additional Feature first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the Plan.
        $PlanAdditionalFeature->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Plan Additional Feature Destroyed',
            'status_code' => 200
        ]);
    }
}
