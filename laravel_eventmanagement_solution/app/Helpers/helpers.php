<?php

use App\Models\Cms;
use App\Models\Currency;
use App\Models\FavouriteArtist;
use App\Models\GeneralSetting;
use App\Models\SocialLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

if (!function_exists('generalSetting')) {
    function generalSetting()
    {
        $data = GeneralSetting::first();

        return $data;
    }
}

if (!function_exists('currency')) {
    function currency()
    {
        $currency = Currency::all();
        return $currency;
    }
}
if (!function_exists('CmsPages')) {
    function CmsPages()
    {

        $CmsPageData = Cms::where('status', 1)->get();

        return $CmsPageData;
    }
}

// Social Links
if (!function_exists('socialLinks')) {
    function SocialLinks()
    {
        $SocialLinksData = SocialLink::all()->where('status', 1);
        return $SocialLinksData;
    }
}
if (!function_exists('dmy_to_yyymmdd')) {
    function dmy_to_yyymmdd($date)
    {
        return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
    }
}

if (!function_exists('getfavcount')) {
    function getfavcount($id)
    {
        $favcount = FavouriteArtist::where('artist_id', $id)->where('user_id', Auth::user()->id)->pluck('id');

        return $favcount;
    }
}
