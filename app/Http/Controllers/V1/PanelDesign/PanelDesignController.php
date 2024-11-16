<?php

namespace App\Http\Controllers\V1\PanelDesign;

use App\Models\PanelDesign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PanelDesignController extends Controller
{
    public function index(Request $request)
    {
        // Get all request parameters
        $featureName = $request->feature_name;
        $featureId = $request->feature_id;
        $panelColumnId = $request->panel_column_id;
        $templateId = $request->template_id;
        $status = $request->status;

        // Start query builder
        $query = PanelDesign::query();

        // Apply filters
        if ($featureName) {
            $query->where('feature_name', $featureName);
        }

        if ($featureId) {
            $query->where('feature_id', $featureId);
        }

        if ($panelColumnId) {
            $query->where('panel_column_id', $panelColumnId);
        }

        if ($templateId) {
            $query->where('template_id', $templateId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Execute the query and get the results
        $panelDesigns = $query->get();

        return response()->json([
            'data' => $panelDesigns,
            'message' => 'Panel designs retrieved successfully',
            'status_code' => 200
        ]);
    }

    public function show($id)
    {
        $panelDesign = PanelDesign::find($id);

        if (!$panelDesign) {
            return response()->json([
                'data' => null,
                'message' => 'panelDesign not found',
                'status_code' => 400,
            ], 400);
        }

        return response()->json([
            'data' => $panelDesign,
            'message' => 'Success',
            'status_code' => 200
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feature_name' => 'required',
            // 'panel_column_id' => 'required',
            'template_id' => 'required|exists:templates,id',
            'feature_id' => 'required|exists:features,id',
            'status' => 'required|in:active,pause,terminated',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $featureName = $request->feature_name;
        $featureId = $request->feature_id;
        $panelColumnId = $request->panel_column_id;

        // Check if the combination of feature_name and feature_id already exists
        $existingPanelDesign = PanelDesign::where('feature_name', $featureName)
            ->where('feature_id', $featureId)
            ->first();

        if ($existingPanelDesign) {
            return response()->json([
                'data' => null,
                'message' => 'Panel design with the same feature_name and panel_column_id already exists',
                'status_code' => 400
            ], 400);
        }

        $panelDesign = new PanelDesign();
        $panelDesign->feature_name = $featureName;
        $panelDesign->feature_id = $request->feature_id;
        $panelDesign->panel_column_id = $panelColumnId;
        $panelDesign->template_id = $request->template_id;
        $panelDesign->status = $request->status;
        $panelDesign->save();

        return response()->json([
            'data' => $panelDesign,
            'message' => 'Panel design created successfully',
            'status_code' => 201
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $panelDesign = PanelDesign::find($id);

        if (!$panelDesign) {
            return response()->json([
                'data' => null,
                'message' => 'Panel design not found',
                'status_code' => 400
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'feature_name' => 'required',
            // 'panel_column_id' => 'required',
            'template_id' => 'required|exists:templates,id',
            'feature_id' => 'required|exists:features,id',
            'status' => 'required|in:active,pause,terminated',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $panelDesign->feature_name = $request->feature_name;
        $panelDesign->feature_id = $request->feature_id;
        $panelDesign->panel_column_id = $request->panel_column_id;
        $panelDesign->template_id = $request->template_id;
        $panelDesign->status = $request->status;
        $panelDesign->save();

        // Update the updated_at timestamp
        $panelDesign->touch();

        return response()->json([
            'data' => $panelDesign,
            'message' => 'Panel design updated successfully',
            'status_code' => 200
        ], 200);
    }

    public function delete($id)
    {
        // Get the panel design with the given ID.
        $panelDesign = PanelDesign::find($id);

        // Return a 404 response if the panel design doesn't exist
        if (!$panelDesign) {
            return response()->json([
                'data' => null,
                'message' => 'Panel Design Not Found',
                'status_code' => 400,
            ], 400);
        }

        // Check if the panel design is already terminated
        if ($panelDesign->status === 'terminated') {
            return response()->json([
                'data' => null,
                'message' => 'Panel Design Already Terminated',
                'status_code' => 400,
            ], 400);
        }

        // Set the status of the panel design to "terminated"
        $panelDesign->status = 'terminated';

        // Save the panel design
        $panelDesign->save();

        // Update the updated_at timestamp
        $panelDesign->touch();

        // Return a success message.
        return response()->json([
            'data' => null,
            'message' => 'Panel Design Deleted',
            'status_code' => 200
        ]);
    }

     // Destroy a PanelDesign
     public function destroy($id)
     {
         // Get the PanelDesign with the given ID.
         $PanelDesign = PanelDesign::find($id);

         // Return a 404 response if the PanelDesign doesn't exist
         if (!$PanelDesign) {
             return response()->json([
                 'data' => null,
                 'message' => 'PanelDesign Already Destroyed.',
                 'status_code' => 400,
             ], 400);
         }

         // Check if the PanelDesign status is "terminated"
         if ($PanelDesign->status !== 'terminated') {
             return response()->json([
                 'data' => null,
                 'message' => 'Please terminate this PanelDesign first.',
                 'status_code' => 400,
             ], 400);
         }

         // Delete the PanelDesign.
         $PanelDesign->delete();

         // Return a success message.
         return response()->json([
             'data' => null,
             'message' => 'PanelDesign Destroyed',
             'status_code' => 200
         ]);
     }
}
