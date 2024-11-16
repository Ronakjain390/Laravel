<?php

namespace App\Http\Controllers\V1\FeatureTopup;

use App\Models\User;
use App\Models\Order;
use App\Models\FeatureTopup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PlanFeatureUsageRecord;
use Illuminate\Support\Facades\Validator;

class FeatureTopupController extends Controller
{

    public function index(Request $request)
    {

        $featureTopups = FeatureTopup::query();

        // Filter by feature ID
        if ($request->has('feature_id')) {
            $featureTopups->where('feature_id', $request->input('feature_id'));
        }

        // Filter by usage limit
        if ($request->has('usage_limit')) {
            $featureTopups->where('usage_limit', $request->input('usage_limit'));
        }

        $featureTopups = $featureTopups->with('feature')->get();
        $featureIds = [];
        if (Auth::guard(Auth::getDefaultDriver())->user()->tokens()->where('name','user')->exists()) {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            $featureIds = PlanFeatureUsageRecord::whereIn('order_id', function ($query) use ($userId) {
                $query->select('id')
                    ->from('orders')
                    ->where('user_id', $userId)
                    ->where('status', 'active');
            })
                ->pluck('feature_id')
                ->toArray();
            $featureTopups = $featureTopups->map(function ($featureTopup) use ($featureIds) {
                $featureTopup->status = in_array($featureTopup->feature_id, $featureIds) ? 'active' : 'inactive';
                return $featureTopup;
            });
        }

        return response()->json([
            'data' => $featureTopups,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function show($id)
    {
        $FeatureTopup = FeatureTopup::find($id)->with('feature')->first();

        if (!$FeatureTopup) {
            return response()->json([
                'data' => null,
                'message' => 'FeatureTopup not found',
                'status_code' => 200,
            ], 200);
        }

        return response()->json([
            'data' => $FeatureTopup,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feature_id' => 'required|integer',
            'price' => 'required|integer|min:1',
            'usage_limit' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $existingFeatureTopup = FeatureTopup::where([['feature_id', $request->feature_id]])->first();

        if ($existingFeatureTopup) {
            // Plan with the same name already exists
            return response()->json([
                'data' => $existingFeatureTopup,
                'message' => 'Feature TopUp already added.',
                'status_code' => 409
            ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
        }

        $FeatureTopup = FeatureTopup::create($request->all());

        return response()->json([
            'data' => $FeatureTopup,
            'message' => 'Feature TopUp Created',
            'status_code' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'feature_id' => 'required|integer',
            'price' => 'required|integer|min:1',
            'usage_limit' => 'required|integer|min:1',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $FeatureTopupexists = FeatureTopup::where([['id', '!=', $id], ['feature_id', $request->feature_id]])->exists();

        if ($FeatureTopupexists) {
            return response()->json([
                'data' => null,
                'message' => 'Feature TopUp already exists',
                'status_code' => 400,
            ], 400);
        }

        $FeatureTopup = FeatureTopup::find($id);

        if (!$FeatureTopup) {
            return response()->json([
                'data' => null,
                'message' => 'Feature TopUp not found',
                'status_code' => 400,
            ], 400);
        }

        $FeatureTopup->update($request->all());

        return response()->json([
            'data' => $FeatureTopup,
            'message' => 'Feature TopUp Updated',
            'status_code' => 200
        ]);
    }

    public function delete($id)
    {
        // Get the Plan with the given ID.
        $FeatureTopup = FeatureTopup::find($id);

        // Delete the Plan.
        // $FeatureTopup->delete();

        // Return a 404 response if the Plan doesn't exist
        if ($FeatureTopup->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Feature TopUp Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the Plan's data.
        $FeatureTopup->status = 'terminated';

        // Save the Plan.
        $FeatureTopup->save();

        // Update the updated_at timestamp
        $FeatureTopup->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Feature TopUp Deleted',
            'status_code' => 200
        ]);
    }


    // Destroy a Plan
    public function destroy($id)
    {
        // Get the Plan with the given ID.
        $FeatureTopup = FeatureTopup::find($id);

        // Return a 404 response if the Plan doesn't exist
        if (!$FeatureTopup) {
            return response()->json([
                'data' => null,
                'message' => 'Feature TopUp Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the Plan status is "terminated"
        if ($FeatureTopup->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this Feature TopUp first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the Plan.
        $FeatureTopup->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Feature TopUp Destroyed',
            'status_code' => 200
        ]);
    }

    public function topupCheckout(Request $request)
    {
        // dd($request->input('topup_ids'));

        $topupId = $request->input('topup_ids'); // Assuming you pass an array of plan IDs
        $topup = FeatureTopup::query();

        // Filter by plan IDs
        if ($topupId) {
            $topup->whereIn('id', $topupId);
        }

        // Get the associated features and additional features
        $topup->with('feature', 'usageRecords');

        $topup = $topup->get();
        // dd($topup);
        return response()->json([
            'data' => $topup,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }
}
