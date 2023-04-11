<?php

use App\Http\Controllers\Frontend\PropertiesController;
use App\Models\Amenities;
use App\Models\GeneralSettingModel;
use App\Models\Portal;
use App\Models\PropertyManager;
use App\Models\SocialLink;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Currency;
use App\Models\Propertytype;

if (!function_exists('getStatus')) {
    function getStatus($id)
    {
        $data = User::where('id', $id)->first();
        return $data;
    }
}

if (!function_exists('masterData')) {
    function masterData()
    {
        $data = GeneralSettingModel::all();
        return $data;
    }
}
if (!function_exists('masterDataContact')) {
    function masterDataContact()
    {
        $data = Currency::where('default', 1)->get();
        return $data;
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

if (!function_exists('CmsPages')) {
    function CmsPages()
    {

        $CmsPageData = Portal::where('status', 1)->get();

        return $CmsPageData;
    }
}

if (!function_exists('CmsGuidePages')) {
    function CmsGuidePages()
    {

        $CmsGuideData = Portal::where('type', 'guides')->get();

        return $CmsGuideData;
    }
}
if (!function_exists('Currency')) {
    function Currency()
    {

        $Currency = Currency::where('default', 1)->get();

        return $Currency;
    }
}

if (!function_exists('ptype')) {
    function ptype()
    {

        $ptype = Propertytype::where('status',1)->get();

        return $ptype;
    }
}

if (!function_exists('aminities')) {
    function aminities()
    {

        $aminities = Amenities::where('status', 1)->get();

        return $aminities;
    }
}
