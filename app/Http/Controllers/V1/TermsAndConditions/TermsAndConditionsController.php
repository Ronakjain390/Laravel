<?php

namespace App\Http\Controllers\V1\TermsAndConditions;

use App\Http\Controllers\Controller;
use App\Models\TermsAndConditions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TermsAndConditionsController extends Controller
{
    public function index()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $request = request();
        $panelId = $request->input('panel_id');
        $sectionId = $request->input('section_id');

        $query = TermsAndConditions::where('user_id', $userId)
                                    ->where('panel_id', $panelId) ;

        if ($panelId !== null) {
            $query->where('panel_id', $panelId);
        }

        if ($sectionId !== null) {
            $query->where('section_id', $sectionId);
        }

        $termsandconditions = $query->get();
        // dd($termsandconditions);
        return response()->json([
            'data' => $termsandconditions,
            'message' => 'Terms and Conditions retrieved successfully',
            'status_code' => 200,
        ], 200);
    }


    public function store(Request $request)
    {
        // dd($request->panel_id);
        $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Define the validation rules
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'user_id' => 'exists:users,id',
            'panel_id' => 'required', // Add validation for panel_id
            'section_id' => 'required', // Add validation for section_id
        ]);

        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Create a new TermsAndConditions instance and set the properties
        $termsandconditions = TermsAndConditions::create([
            'content' => $request->content,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
            'panel_id' => $request->panel_id,
            'section_id' => $request->section_id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $termsandconditions,
            'message' => 'Terms and Conditions created successfully',
            'status_code' => 201,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);
        // Determine the user ID based on the authentication guard
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Find the TermsAndConditions instance by ID and user_id
        $termsandconditions = TermsAndConditions::where('user_id', $userId)->findOrFail($id);

        // Define the validation rules
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'user_id' => 'exists:users,id', // Adjusted to match the store method
            'panel_id' => 'required',
            'section_id' => 'required',
        ]);

        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Update the TermsAndConditions instance
        $termsandconditions->content = $request->input('content');
        $termsandconditions->panel_id = $request->input('panel_id'); // Assuming you want to update this as well
        $termsandconditions->section_id = $request->input('section_id'); // Assuming you want to update this as well
        $termsandconditions->save();

        return response()->json([
            'success' => true,
            'data' => $termsandconditions,
            'message' => 'Terms and Conditions updated successfully',
            'status_code' => 200,
        ], 200);
    }

    public function destroy($id)
    {
        $termsandconditions = TermsAndConditions::find($id);

        if (!$termsandconditions) {
            return response()->json([

                'data' => null,
                'message' => 'Terms and Conditions not found.',
                'status_code' => 400,
            ], 400);
        }

        $termsandconditions->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Terms and Conditions deleted successfully.',
            'status_code' => 200,
        ]);
    }
}
// public function store(Request $request)
// {
//     // Validate the request data
//     $validator = Validator::make($request->all(), [
//         'contents' => 'required|array',
//         'contents.*' => 'required|string',
//         'panel_id' => 'required|integer',
//         'section_id' => 'required|integer',
//     ]);

//     // Check if validation fails
//     if ($validator->fails()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Validation error.',
//             'errors' => $validator->errors(),
//         ], 400);
//     }

//     $user = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
//     $contents = $request->input('contents');

//     $savedTermsAndConditions = [];

//     foreach ($contents as $content) {
//         $savedTermsAndConditions[] = TermsAndConditions::create([
//             'content' => $content,
//             'user_id' => $user,
//             'panel_id' => $request->panel_id,
//             'section_id' => $request->section_id,
//         ]);
//     }

//     return response()->json([
//         'success' => true,
//         'data' => $savedTermsAndConditions,
//         'message' => 'Terms and Conditions created successfully',
//         'status_code' => 201,
//     ], 201);
// }
