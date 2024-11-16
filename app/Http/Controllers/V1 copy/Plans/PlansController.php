<?php

namespace App\Http\Controllers\Web\Plans;

use App\Models\Plan;
use App\Models\Plans;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PlansController extends Controller
{

    public function index(Request $request)
    {
        $plans = Plans::query();

        // Filter by plan name
        if ($request->has('plan_name')) {
            $plans->where('plan_name', 'like', '%' . $request->input('plan_name') . '%');
        }

        // Filter by section ID
        if ($request->has('section_id')) {
            $plans->where('section_id', $request->input('section_id'));
        }

        // Filter by panel ID
        if ($request->has('panel_id')) {
            $plans->where('panel_id', $request->input('panel_id'));
        }

        // Filter by enterprise plan status
        if ($request->has('is_enterprise_plan')) {
            $plans->where('is_enterprise_plan', $request->input('is_enterprise_plan'));
        }

        // Filter by enterprise user ID
        if ($request->has('enterprise_user_id')) {
            $plans->where('enterprise_user_id', $request->input('enterprise_user_id'));
        }

        // Filter by validity days
        if ($request->has('validity_days')) {
            $plans->where('validity_days', $request->input('validity_days'));
        }

        // Filter by status
        if ($request->has('status')) {
            $plans->where('status', $request->input('status'));
        }

        // Get the associated features and additional features
        $plans->with('features', 'additionalFeatures');

        $plans = $plans->get();

        return response()->json([
            'data' => $plans,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }


public function show($id)
{
    $Plan = Plans::find($id)->with('features', 'additionalFeatures')->first();

    if (!$Plan) {
        return response()->json([
            'data' => null,
            'message' => 'Plan not found',
            'status_code' => 400,
        ], 400);
    }

    return response()->json([
        'data' => $Plan,
        'message' => 'Success',
        'status_code' => 200
    ]);
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_name' => 'required|max:255',
            'price' => 'required|integer',
            'section_id' => 'required|integer|exists:section,id',
            'panel_id' => 'required|integer|exists:panel,id',
            'is_enterprise_plan' => 'integer|nullable',
            'enterprise_user_id' => 'integer|nullable',
            'validity_days' => 'integer',
            'status' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $existingPlan = Plans::where('plan_name', $request->plan_name)
        ->where('panel_id', $request->panel_id)
        ->first();

        if ($existingPlan) {
            // Plan with the same name already exists
            return response()->json([
                'data' => $existingPlan,
                'message' => 'Plan already exists.',
                'status_code' => 409
            ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
        }

        $Plan = Plans::create($request->all());

        return response()->json([
            'data' => $Plan,
            'message' => 'Plan Created',
            'status_code' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'plan_name' => 'required|max:255',
            'price' => 'required|integer',
            'section_id' => 'required|integer|exists:section,id',
            'panel_id' => 'required|integer|exists:panel,id',
            'is_enterprise_plan' => 'integer|nullable',
            'enterprise_user_id' => 'integer|nullable',
            'validity_days' => 'integer',
            'status' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $Plan = Plans::find($id);

        if (!$Plan) {
            return response()->json([
                'data' => null,
                'message' => 'Plan not found',
                'status_code' => 400,
            ], 400);
        }

        $Plan->update($request->all());

        return response()->json([
            'data' => $Plan,
            'message' => 'Plan Updated',
            'status_code' => 200
        ]);
    }

    public function delete($id)
    {
        // Get the Plan with the given ID.
        $Plan = Plans::find($id);

        // Delete the Plan.
        // $Plan->delete();

        // Return a 404 response if the Plan doesn't exist
        if ($Plan->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Plan Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the Plan's data.
        $Plan->status = 'terminated';

        // Save the Plan.
        $Plan->save();

        // Update the updated_at timestamp
        $Plan->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Plan Deleted',
            'status_code' => 200
        ]);
    }


    // Destroy a Plan
    public function destroy($id)
    {
        // Get the Plan with the given ID.
        $Plan = Plans::find($id);

        // Return a 404 response if the Plan doesn't exist
        if (!$Plan) {
            return response()->json([
                'data' => null,
                'message' => 'Plan Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the Plan status is "terminated"
        if ($Plan->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this Plan first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the Plan.
        $Plan->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Plan Destroyed',
            'status_code' => 200
        ]);
    }
}
