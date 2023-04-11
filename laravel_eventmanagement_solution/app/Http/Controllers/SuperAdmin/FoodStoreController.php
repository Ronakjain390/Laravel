<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FoodStore;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FoodStoreController extends Controller
{
    public function index()
    {
        $data = FoodStore::all();
        return view('SuperAdmin/Pages/FoodStores/Index', compact('data'));
    }

    public function create()
    {
        return view('SuperAdmin/Pages/FoodStores/Create');
    }

    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required',
            'email' => 'required | email',
            //     'contact_number' => 'required | digits_between:10,15',
            'description' => 'required',
            // 'country' => 'max:20',
            // 'state' => 'max:20',
            // 'city' => 'max:20',
            // 'address' => 'max:200',
            //     'postal_code' => 'digits_between: 4,8',
            'image' => 'required | mimes:jpg, png, jpeg | max:2048',
            'video' => 'mimes:mp4,mov,ogg,qt | max:20000'
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }

        $image = time() . '.' . request()->file('image')->getClientOriginalExtension();

        request()->image->move(public_path('Assets/images'), $image);

        if ($request->video) {
            $file_name = time() . '.' . request()->file('video')->getClientOriginalExtension();

            request()->video->move(public_path('Assets/images'), $file_name);
        } else {
            $file_name = "";
        }

        $data = new FoodStore();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->contact_number = $request->contact_number;
        $data->description = $request->description;
        $data->address = $request->address;
        $data->country = $request->country;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->postal_code = $request->postal_code;
        $data->image = $image;
        $data->video = $file_name;
        // $data->itemid = $request->itemid;
        if ($data->longitude) {
            $data->longitude = $request->longitude;
        }
        if ($data->latitude) {
            $data->latitude = $request->latitude;
        }
        if ($request->hasfile('gallary_image')) {
            foreach ($request->file('gallary_image') as $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path('Assets/images'), $name);
                $imgData[] = $name;
            }

            $data->gallary_images = json_encode($imgData);
        }

        if ($request->status == 'on') {
            $data->status = 1;
        } else {
            $data->status = 0;
        }
        $data->save();

        $food_store_id = $data->id;

        $insert_data = [];
        for ($i = 0; $i < count($request->food_name); $i++) {
            if (isset($request->food_name[$i]) && isset($request->food_description[$i]) && isset($request->food_image[$i]) && isset($request->food_price[$i])) {
                $food_image = date('mdYHis') . uniqid() . '.' . $request->food_image[$i]->extension();

                request()->food_image[$i]->move(public_path('Assets/images/'), $food_image);

                if (isset($request->food_video[$i])) {
                    $food_video = date('mdYHis') . uniqid() . '.' . $request->food_video[$i]->extension();

                    request()->food_video[$i]->move(public_path('Assets/images/'), $food_video);
                } else {
                    $food_video = null;
                }

                if ($request->food_status[$i] == 'on') {
                    $food_status[$i] = 1;
                } else {
                    $food_status = 0;
                }
                $data = array(
                    'name' => $request->food_name[$i],
                    'description'  => $request->food_description[$i],
                    'image'  => $food_image,
                    'video' => $food_video,
                    'price' => $request->food_price[$i],
                    'status' => $food_status[$i],
                    'food_store_id' => $food_store_id,
                );
                $insert_data[] = $data;
            }
        }

        DB::table('food_store_menu')->insert($insert_data);

        return redirect()->route('FoodStores')->with('success', 'Food Store has been created successfully.');
    }

    public function edit($id)
    {
        $data = FoodStore::find($id);

        $food_menu = DB::table('food_store_menu')->where('food_store_id', $id)->get();
        // dd($food_menu);
        return view('SuperAdmin/Pages/FoodStores/Edit', compact('data', 'food_menu'));
    }

    public function update(Request $request, $id)
    {
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required',
            'email' => 'required | email',
            //     'contact_number' => 'required | digits_between:10,15',
            'description' => 'required',
            // 'country' => 'max:20',
            // 'state' => 'max:20',
            // 'city' => 'max:20',
            // 'address' => 'max:200',
            //     'postal_code' => 'digits_between: 4,8',
            'image' => 'required | mimes:jpg, png, jpeg | max:2048',
            'video' => 'mimes:mp4,mov,ogg,qt | max:20000'
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors());
        }
        $data = FoodStore::find($id);

        $image = $request->hidden_Image;

        if ($request->image != '') {
            $image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images'), $image);
        } else {
            $img = FoodStore::find($id)->first();

            $image = $img->image;
        }

        $video = $request->hidden_Video;
        if ($request->video != '') {
            $video = time() . '.' . request()->video->getClientOriginalExtension();

            request()->video->move(public_path('Assets/images'), $video);
        } else {
            $video1 = FoodStore::find($id)->first();

            $video = $img->video;
        }

        $data->name = $request->name;
        $data->email = $request->email;
        $data->contact_number = $request->contact_number;
        $data->description = $request->description;
        $data->address = $request->address;
        $data->country = $request->country;
        $data->state = $request->state;
        $data->city = $request->city;
        $data->postal_code = $request->postal_code;
        $data->image = $image;
        $data->video = $video;
        $data->longitude = $request->longitude;
        $data->latitude = $request->latitude;
        if ($request->hasfile('gallary_image')) {
            foreach ($request->file('gallary_image') as $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path('Assets/images'), $name);
                $imgData[] = $name;
            }

            $data->gallary_images = json_encode($imgData);
        }

        if ($request->status == 'on') {
            $data->status = 1;
        } else {
            $data->status = 0;
        }

        $data->save();

        $insert_data = [];
        if ($request->food_name || $request->food_description || $request->food_image || $request->food_price) {
            DB::table('food_store_menu')->where('food_store_id', $id)->delete();
            for ($i = 0; $i < count($request->food_name); $i++) {
                if (isset($request->food_name[$i]) && isset($request->food_description[$i]) && isset($request->food_image[$i]) && isset($request->food_price[$i])) {
                    $food_image = date('mdYHis') . uniqid() . '.' . $request->food_image[$i]->extension();

                    request()->food_image[$i]->move(public_path('Assets/images'), $food_image);
                    if (isset($request->food_video[$i])) {
                        $food_video = date('mdYHis') . uniqid() . '.' . $request->food_video[$i]->extension();

                        request()->food_video[$i]->move(public_path('Assets/images'), $food_video);
                    } else {
                        $food_video = null;
                    }

                    if ($request->food_status == 'on') {
                        $food_status = 1;
                    } else {
                        $food_status = 0;
                    }
                    $data = array(
                        'name' => $request->food_name[$i],
                        'description'  => $request->food_description[$i],
                        'image'  => $food_image,
                        'video' => $food_video,
                        'price' => $request->food_price[$i],
                        'status' => $food_status,
                        'food_store_id' => $id
                    );
                    $insert_data[] = $data;
                }
            }

            DB::table('food_store_menu')->insert($insert_data);
        }

        return redirect()->route('FoodStores')
            ->with('success', 'Food Store has been updated successfully');
    }

    public function show($id)
    {
        $data = FoodStore::where('id', $id)->first();
        $food_menu = DB::table('food_store_menu')->where('food_store_id', $id)->get();
        return view('SuperAdmin/Pages/FoodStores/Show', compact('data', 'food_menu'));
    }

    public function delete($id)
    {
        FoodStore::where('id', $id)->delete();
        DB::table('food_store_menu')->where('food_store_id', $id)->delete();

        return redirect()->route('FoodStores')->with('error', 'Food Store Has Been Deleted successfully');
    }
}
