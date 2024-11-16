<?php

namespace App\Http\Controllers\V1\Templates;

use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TemplatesController extends Controller
{
    //

    public function index(Request $request)
    {
        $query = Template::query();

        // Filter by template_name
        if ($request->has('template_name')) {
            $query->where('template_name',$request->template_name );
        }

        // Filter by template_page_name
        if ($request->has('template_page_name')) {
            $query->where('template_page_name',  $request->template_page_name );
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Get filtered templates
        $templates = $query->get();

        return response()->json([
            'data' => $templates,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'template_page_name' => 'required|string|max:255',
            'status' => 'required|in:active,pause,terminated',
            'template_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $existingTemplate = Template::where('template_name', $request->template_name)->first();

        if ($existingTemplate) {
            return response()->json([
                'data' => $existingTemplate,
                'message' => 'Template with the same name already exists.',
                'status_code' => 409
            ], 409);
        }

        $template = new Template();
        $template->template_name = $request->template_name;
        $template->template_page_name = $request->template_page_name;
        $template->status = $request->status;

        // Upload and store the template image in Amazon S3
        if ($request->hasFile('template_image')) {
            $image = $request->file('template_image');
            $imagePath = 'templates/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Remove the older image if it exists
            if ($template->template_image && Storage::disk('s3')->exists($template->template_image)) {
                Storage::disk('s3')->delete($template->template_image);
            }

            Storage::disk('s3')->put($imagePath, file_get_contents($image));
            $template->template_image = $imagePath;
        }

        $template->save();

        return response()->json([
            'data' => $template,
            'message' => 'Template created successfully',
            'status_code' => 201
        ], 201);
    }

    public function show($id)
    {
        $template = Template::find($id);

        if (!$template) {
            return response()->json([
                'data' => null,
                'message' => 'Template not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $template,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string|max:255',
            'template_page_name' => 'required|string|max:255',
            'status' => 'required|in:active,pause,terminated',
            'template_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        $template = Template::find($id);

        if (!$template) {
            return response()->json([
                'data' => null,
                'message' => 'Template not found',
                'status_code' => 400,
            ], 400);
        }

        $template->template_name = $request->template_name;
        $template->template_page_name = $request->template_page_name;
        $template->status = $request->status;

        // Upload and store the template image in Amazon S3 if provided
        if ($request->hasFile('template_image')) {
            $image = $request->file('template_image');
            $imagePath = 'templates/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Remove the older image if it exists
            if ($template->template_image && Storage::disk('s3')->exists($template->template_image)) {
                Storage::disk('s3')->delete($template->template_image);
            }

            Storage::disk('s3')->put($imagePath, file_get_contents($image));
            $template->template_image = $imagePath;
        }

        $template->save();

        // Update the updated_at timestamp
        $template->touch();

        return response()->json([
            'data' => $template,
            'message' => 'Template updated successfully',
            'status_code' => 200
        ], 200);
    }

    public function delete($id)
    {
        // Get the template with the given ID.
        $template = Template::find($id);

        // Return a 404 response if the template doesn't exist.
        if (!$template) {
            return response()->json([
                'data' => null,
                'message' => 'Template not found',
                'status_code' => 400,
            ], 400);
        }

        // Return a 404 response if the template is already terminated.
        if ($template->status == 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Template already deleted.',
                'status_code' => 400,
            ], 400);
        }

        // Update the template status to "terminated".
        $template->status = 'terminated';

        // Save the updated template.
        $template->save();

        // Update the updated_at timestamp.
        $template->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Template deleted successfully',
            'status_code' => 200
        ], 200);
    }

    public function destroy($id)
    {
        // Get the template with the given ID.
        $template = Template::find($id);

        // Return a 404 response if the template doesn't exist.
        if (!$template) {
            return response()->json([
                'data' => null,
                'message' => 'Template Already Destroyed',
                'status_code' => 400,
            ], 400);
        }

        // Check if the section status is "terminated"
        if ($template->status !== 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Please terminate this template first.',
                'status_code' => 400,
            ], 400);
        }

       // Delete the template image if it exists.
       if ($template->template_image && Storage::disk('s3')->exists($template->template_image)) {
        Storage::disk('s3')->delete($template->template_image);
    }

        // Delete the template.
        $template->delete();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Template destroyed successfully',
            'status_code' => 200
        ], 200);
    }
}
