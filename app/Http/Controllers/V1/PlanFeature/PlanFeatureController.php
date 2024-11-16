<?php

namespace App\Http\Controllers\V1\PlanFeature;

use App\Models\PlanFeature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PlanFeatureController extends Controller
{
    //

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:plans,id',
            'feature_id' => 'required|integer|exists:features,id',
            'feature_usage_limit' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $existingPlanFeature = PlanFeature::where([['plan_id',$request->plan_id],['feature_id',$request->feature_id]])
        ->first();

        if ($existingPlanFeature) {
            // Plan with the same name already exists
            return response()->json([
                'data' => $existingPlanFeature,
                'message' => 'Plan Feature already added.',
                'status_code' => 409
            ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
        }

        $PlanFeature = PlanFeature::create($request->all());

        return response()->json([
            'data' => $PlanFeature,
            'message' => 'Plan Feature Created',
            'status_code' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer|exists:plans,id',
            'feature_id' => 'required|integer|exists:features,id',
            'feature_usage_limit' => 'required|integer|min:1',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $PlanFeatureexists = PlanFeature::where([['id','!=',$id],['plan_id',$request->plan_id],['feature_id',$request->feature_id]])->exists();

        if ($PlanFeatureexists) {
            return response()->json([
                'data' => null,
                'message' => 'Plan Feature already exists',
                'status_code' => 400,
            ], 400);
        }
        $PlanFeature = PlanFeature::where('id',$id);

        if (!$PlanFeature) {
            return response()->json([
                'data' => null,
                'message' => 'Plan Feature not found',
                'status_code' => 400,
            ], 400);
        }

        $PlanFeature->update($request->all());

        return response()->json([
            'data' => $PlanFeature,
            'message' => 'Plan Feature Updated',
            'status_code' => 200
        ]);
    }

    public function delete($id)
    {
        // Get the Plan with the given ID.
        $PlanFeature = PlanFeature::where('id',$id);

        // Delete the Plan.
        // $PlanFeature->delete();

        // Return a 404 response if the Plan doesn't exist
        if ($PlanFeature->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Plan Feature Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the Plan's data.
        $PlanFeature->status = 'terminated';

        // Save the Plan.
        $PlanFeature->save();

        // Update the updated_at timestamp
        $PlanFeature->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Plan Feature Deleted',
            'status_code' => 200
        ]);
    }


    // Destroy a Plan
    public function destroy($id)
    {
        // Get the Plan with the given ID.
        $PlanFeature = PlanFeature::where('id',$id);

        // Return a 404 response if the Plan doesn't exist
        if (!$PlanFeature) {
            return response()->json([
                'data' => null,
                'message' => 'Plan Feature Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the Plan status is "terminated"
        if ($PlanFeature->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this Plan Feature first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the Plan.
        $PlanFeature->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Plan Feature Destroyed',
            'status_code' => 200
        ]);
    }


}
