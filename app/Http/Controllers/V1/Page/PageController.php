<?php

namespace App\Http\Controllers\V1\Page;
use App\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::all();
        return response()->json(['pages' => $pages], Response::HTTP_OK);
    }

    public function create()
    {
        // Not needed for API
    }
        public function show(Request $request, $slug)
    {
        // dd($slug);
        $page = Page::where('slug', $slug)->firstOrFail();
        // dd($page);
        return response()->json([
            'message' => 'created successfully.',
            'page' => $page, 
            'status_code' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        // dd($request);
        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'slug' => 'required|unique:pages',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 422,
            ], 422);
        }

        // Create page
        // $page = Page::create($validator);

        $page = new Page([
            'title' => $request->title,
            'content' => $request->content,
            'slug' => $request->slug,
      
        ]);
        $page->save();
 
        return response()->json([
            'message' => 'created successfully.',
            'page' => $page, 
            'status_code' => 200
        ], 200);
    }

    public function edit(Page $page)
    {
        // Not needed for API
    }

    public function update(Request $request, Page $page)
    {
        // Validation
        $validatedData = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'slug' => 'required|unique:pages,slug,' . $page->id,
        ]);

        // Update page
        $page->update($validatedData);

        // return response()->json(['page' => $page], Response::HTTP_OK);
        return response()->json([
            'page' => $page,
            'message' => 'Updated sucessfully',
            'status_code' => 200
        ]);
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
