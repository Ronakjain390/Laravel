<?php

namespace App\Http\Controllers\V1\TeamUserPermission;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TeamUserPermission;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
// use App\Http\Controllers\V1\TeamUserPermission\TeamUserPermissionController;

class TeamUserPermissionController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'team_user_id' => 'required|integer',
            'team_id' => 'required|integer',
            'team_owner_user_id' => 'required|integer',
            'permission' => 'required',
            'status' => Rule::in(['active', 'pause', 'terminated']),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create and save a new TeamUserPermission record
        $teamUserPermission = new TeamUserPermission($request->all());
        $teamUserPermission->save();

        return response()->json(['message' => 'TeamUserPermission created successfully'], 200);
    }

    public function index()
    {
        // Get a list of TeamUserPermissions
        $teamUserPermissions = TeamUserPermission::all();

        return response()->json(['data' => $teamUserPermissions], 200);
    }

    public function show($id)
    {
        // Find a specific TeamUserPermission by ID
        $teamUserPermission = TeamUserPermission::where('team_user_id',$id)->first();
        // dd($teamUserPermission);
        if (!$teamUserPermission) {
            return response()->json(['errors' => ['TeamUserPermission not found']], 404);
        }

        return response()->json(['data' => $teamUserPermission], 200);
    }

    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'team_id' => 'integer',
            'permission' => 'required',
            // 'status' => Rule::in(['active', 'pause', 'terminated']),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find and update the TeamUserPermission record
        $teamUserPermission = TeamUserPermission::find($id);

        if (!$teamUserPermission) {
            return response()->json(['errors' => ['Team Member Permission not found']], 404);
        }

        $teamUserPermission->update($request->all());

        return response()->json(['message' => 'Team Member Permission updated successfully'], 200);
    }

    public function destroy($id)
    {
        // Find and delete the TeamUserPermission record
        $teamUserPermission = TeamUserPermission::where('team_user_id',$id)->first();

        if (!$teamUserPermission) {
            return response()->json(['errors' => ['Team Member Permission not found']], 404);
        }

        $teamUserPermission->delete();

        return response()->json(['message' => 'Team Member Permission deleted successfully'], 200);
    }
}
