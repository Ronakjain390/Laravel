<?php

namespace App\Http\Controllers\EventOrganizer;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Expertize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerArtistController extends Controller
{
    public function index()
    {
        $data = Artist::where('type', '2')->orWhere('user_id', Auth::user()->id )->get();
        return view('EventOrgnizer/Pages/Artists/Index', compact('data'));
    }

    public function create()
    {
        $data = Expertize::where('status', 1)->get();
        return view('EventOrgnizer/Pages/Artists/Create', compact('data'));
    }

    public function store(Request $request)
    {
        // echo $request->image;die;
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required',
            // 'nick_name' => 'string',
            'email' => 'required | email',
            'contact_number' => 'required | digits_between:10,15',
            'bio' => 'required',
            'country' => 'required | max:20',
            'image' => 'required | mimes:jpg, png, jpeg ',
            'gallary_images' => 'mimes:jpg, png, jpeg | max:2048',
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

        $data = new Artist();
        $data->name = $request->name;
        $data->nick_name = $request->nick_name;
        $data->email = $request->email;
        $data->contact_number = $request->contact_number;
        $data->bio = $request->bio;
        $data->country = $request->country;
        $data->image = $image;
        $data->user_id = Auth::user()->id;
        $data->type = '2';
        $data->video = $file_name;
        if ($request->hasfile('gallary_images')) {
            foreach ($request->file('gallary_images') as $file) {
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

        $artist_id = $data->id;

        $artist_exp = $request->expertize;
        if ($artist_exp) {
            foreach ($artist_exp as $data) {
                DB::table('artist_expertize')->insert([
                    'expertize_id' => $data,
                    'artist_id' => $artist_id
                ]);
            }
        }
        return redirect()->route('ArtistsOrganizer')
            ->with('success', 'Artist has been created successfully.');
    }

    public function edit($id)
    {
        $data = Artist::find($id);
        $art_expe = DB::table('artist_expertize')->where('artist_id', $id)->pluck('expertize_id')->toArray();
        $art_expertize = Expertize::whereIn('id', $art_expe)->get();
        $exp = Expertize::where('status', 1)->get();
        return view('EventOrgnizer/Pages/Artists/Edit', compact('data', 'art_expertize', 'exp'));
    }

    public function update(Request $request, $id)
    {
        $data = Artist::find($id);
        if ($request->status == 'on') {
            $data->status = 1;
        } else {
            $data->status = 0;
        }

        $image = $request->hidden_image;

        if ($request->image != '') {
            $image = time() . '.' . request()->image->getClientOriginalExtension();

            request()->image->move(public_path('Assets/images'), $image);
        }

        $Video = $request->hidden_Video;

        if ($request->video != '') {
            $Video = time() . '.' . request()->video->getClientOriginalExtension();

            request()->video->move(public_path('Assets/images'), $Video);
        }

        $data->name = $request->name;
        $data->nick_name = $request->nick_name;
        $data->email = $request->email;
        $data->contact_number = $request->contact_number;
        $data->bio = $request->bio;
        $data->country = $request->country;
        $data->image = $image;
        $data->video = $Video;
        if ($request->hasfile('gallary_images')) {
            foreach ($request->file('gallary_images') as $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path('Assets/images'), $name);
                $imgData[] = $name;
            }

            $data->gallary_images = json_encode($imgData);
        }
        $data->save();
        if ($request->expertize) {
            DB::table('artist_expertize')->where('artist_id', $id)->delete();

            $artist_exp[] = $request->expertize;
            foreach ($artist_exp as $data) {
                DB::table('artist_expertize')->insert([
                    'expertize_id' => $data,
                    'artist_id' => $id

                ]);
            }
        }

        return redirect()->route('ArtistsOrganizer')
            ->with('success', 'Artist has been updated successfully');
    }

    public function show($id)
    {
        $item = Artist::where('id', $id)->first();
        $art_expe = DB::table('artist_expertize')->where('artist_id', $id)->pluck('expertize_id')->toArray();
        $exp1 = Expertize::whereIn('id', $art_expe)->get();

        return view('EventOrgnizer/Pages/Artists/Show', compact('item', 'exp1'));
    }

    public function delete($id)
    {
        Artist::where('id', $id)->delete();
        DB::table('artist_expertize')->where('artist_id', $id)->delete();
        return redirect()->route('ArtistsOrganizer')->with('error', 'Artist Has Been Deleted successfully');
    }
}
