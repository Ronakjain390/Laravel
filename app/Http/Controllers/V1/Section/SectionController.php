<?php

namespace App\Http\Controllers\V1\Section;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the status and section values from the request
        $status = $request->input('status');
        $section = $request->input('section');

        // Query sections based on the filter parameters
        $query = Section::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($section) {
            $query->where('section', $section);
        }

        // Get the filtered sections
        $sections = $query->get();

        // Return the sections
        return response()->json([
            'data' => $sections,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }



    public function show($id)
    {
        // Get the section with the given ID.
        $section = Section::find($id);

        if (!$section) {
            return response()->json([
                'data' => null,
                'message' => 'Section not found',
                'status_code' => 400,
            ], 400);
        }

        // Return the section.
        return response()->json([
            'data' => $section,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,pause,terminated'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $existingSection = Section::where('section', $request->section)->first();

        if ($existingSection) {
            // Section with the same name already exists
            return response()->json([
                'data' => $existingSection,
                'message' => 'Section already exists.',
                'status_code' => 409
            ], 409); // 409 Conflict status code indicating a conflict with the current state of the resource
        }

        $section = new Section();
        $section->section = $request->section;
        $section->status = $request->status;
        $section->save();

        return response()->json([
            'data' => $section,
            'message' => 'Section Created',
            'status_code' => 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'section' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,pause,terminated'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get the section with the given ID.
        $section = Section::find($id);

        // Return a 404 response if the section doesn't exist
        if (!$section) {
            return response()->json([
                'data' => null,
                'message' => 'Section not found.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the section's data.
        $section->section = $request->section;
        $section->status = $request->status;

        // Save the section.
        $section->save();

        // Update the updated_at timestamp
        $section->touch();

        return response()->json([
            'data' => $section,
            'message' => 'Section Updated',
            'status_code' => 200
        ]);
    }

    public function delete($id)
    {
        // Get the section with the given ID.
        $section = Section::find($id);

        // Delete the section.
        // $section->delete();

        // Return a 404 response if the section doesn't exist
        if ($section->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Section Already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Fill in the section's data.
        $section->status = 'terminated';

        // Save the section.
        $section->save();

        // Update the updated_at timestamp
        $section->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Section Deleted',
            'status_code' => 200
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Get the section with the given ID.
        $section = Section::find($id);

        // Return a 404 response if the section doesn't exist
        if (!$section) {
            return response()->json([
                'data' => null,
                'message' => 'Section Already Destroyed.',
                'status_code' => 400,
            ], 400);
        }

        // Check if the section status is "terminated"
        if ($section->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this section first.',
                'status_code' => 400,
            ], 400);
        }

        // Delete the section.
        $section->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Section Destroyed',
            'status_code' => 200
        ]);
    }

}
