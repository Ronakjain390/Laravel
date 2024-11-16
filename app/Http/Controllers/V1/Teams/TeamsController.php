<?php

namespace App\Http\Controllers\V1\Teams;

use App\Models\Team;
use App\Models\User;
use App\Models\Template;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TeamsController extends Controller
{
    public function index()
    {
        $teams = Team::where('team_owner_user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->get();
        return response()->json(['data' => $teams], 200);
    }

    public function show($id)
    {
        try {
            $team = Team::findOrFail($id);
            return response()->json(['data' => $team], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Team not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_name' => 'required|string',
            'view_preference' => 'required|string',
            'team_owner_user_id' => 'exists:users,id|numeric',
        ]);
        try {

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'status_code' => 422,
                ], 422);
            }
            $team = Team::updateOrInsert(
                ["team_name" => $request->team_name, "team_owner_user_id" => $request->team_owner_user_id],
                ["team_name" => $request->team_name, "team_owner_user_id" => $request->team_owner_user_id, 
                "team_owner_user" => User::where('id', $request->team_owner_user_id)->pluck('name')->first(),
                "team_name_slug" => str_replace(" ", "_", $request->team_name), 
                "view_preference" => $request->view_preference,
                "status" => $request->status
                ]
            );
            return response()->json(['message' => 'Team created', 'data' => $team], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Team creation failed', 'errors' => ['Unexpect Error']], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'team_name' => 'required|string',
        ]);
        // try {
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }
        $team = Team::findOrFail($id);
        $team->update(['team_name' => $request->team_name]);
        return response()->json(['message' => 'Team updated', 'data' => $team], 200);
        // } catch (ValidationException $e) {
        //     return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'Team update failed'], 500);
        // }
    }
    // TeamController

    public function destroy($id)
    {
        try {
            $team = Team::findOrFail($id);
            $team->delete();
            return response()->json(['message' => 'Team deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Team deletion failed'], 500);
        }
    }
}
