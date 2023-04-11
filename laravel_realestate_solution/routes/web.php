<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\Auth\ForgotPasswordController;


/*------------------------------------------
All Admin Controllers List
--------------------------------------------*/
use App\Http\Controllers\Admin\UserManagerController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\TemplatesController;
use App\Http\Controllers\Admin\ReviewsRatingsController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\AgentManagerController;
use App\Http\Controllers\Admin\PropertyEnquiriesController;
use App\Http\Controllers\Admin\NewsLetterController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PortalController;
use App\Http\Controllers\Admin\PropertyManagerController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\AccountSettingController;
use App\Http\Controllers\Admin\SendNewsLetterController;
use App\Http\Controllers\Admin\SmtpMailController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\SubscribedusersController;
use App\Http\Controllers\Admin\AmenitiesController;
use App\Http\Controllers\Admin\CountryNameController;
use App\Http\Controllers\Admin\CurrencyManagerController;
use App\Http\Controllers\Admin\MailSentToController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\PropertyCategoryController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\FlagController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\PostProperty;

/*------------------------------------------
All Frontend Controllers List
--------------------------------------------*/
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\ContactusController;
use App\Http\Controllers\Frontend\SubscribeduserController;
use App\Http\Controllers\Frontend\AdvertiseController;
use App\Http\Controllers\Frontend\NewsblogController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\PropertyListingController;
use App\Http\Controllers\Frontend\PostPropertyController;
use App\Http\Controllers\Frontend\PropertyenquiryController;
use App\Http\Controllers\Frontend\AgentFrontController;
use App\Http\Controllers\Frontend\CmsController;
use App\Http\Controllers\Frontend\FaqController as FrontendFaqController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AgentRegisterController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\UserEnquiryController;
use App\Http\Controllers\Frontend\EmiController;
use App\Http\Controllers\Frontend\AreaConverterController;
use App\Http\Controllers\Frontend\SitemapXmlController;

/*------------------------------------------
All agent Controllers List
--------------------------------------------*/
use App\Http\Controllers\agent\AgentProfileController;
use App\Http\Controllers\agent\AgentPropertyController;
use App\Http\Controllers\agent\AgentEnquiryController;
use Illuminate\Support\Facades\Artisan;

Auth::routes();
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/run-migrations', function () {
    return Artisan::call('migrate', ["--force" => true]);
});

Route::get('/subscribeduserdata', [SubscribeduserController::class, 'store'])->name('subscribeduser.store');

Route::resource('contactus', ContactusController::class);

Route::resource('propertylisting', PropertyListingController::class);
Route::get('propertydetail/{id}', 'App\Http\Controllers\Frontend\PropertyListingController@list')->name('propertydetail');
Route::resource('postproperty', PostPropertyController::class);

Route::get('/propertyenquiry', [PropertyenquiryController::class, 'store'])->name('propertyenquiry.store');
Route::get('/profile', [ProfileController::class, 'indexProfile'])->name('ProfileUser');
Route::post('/profile-update/{id}', [ProfileController::class, 'profileUpdate'])->name('updateProfile');


Route::get('/user-properties', [ProfileController::class, 'userListedProperty'])->name('userPropertyList');
Route::get('/user-property-show/{id}', [ProfileController::class, 'userShowProperty'])->name('userPropertyShow');
Route::get('/user-property-delete/{id}', [ProfileController::class, 'userDeleteProperty'])->name('userPropertyDelete');
Route::get('/user-property-edit/{id}', [ProfileController::class, 'userEditProperty'])->name('userPropertyEdit');
Route::post('/user-property-update/{id}', [ProfileController::class, 'userUpdateProperty'])->name('userPropertyUpdate');
Route::put('/user-property-status-update/{id}', [ProfileController::class, 'userStatusUpdateProperty'])->name('userPropertyStatus');
Route::get('/user-property-create', [ProfileController::class, 'create'])->name('userPropertyCreate');


/*------------------------------------------
--------------------------------------------
All Auth Routes List
--------------------------------------------
--------------------------------------------*/

// Route::get('/change-password', [ResetPasswordController::class, 'changePassword'])->name('changePassword');
// Route::post('/password-changed', [ResetPasswordController::class, 'passwordChange'])->name('passwordChange');

/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::get('admin-login', [LoginController::class, 'adminlogin'])->name('adminLogin');
Route::post('/admin', [LoginController::class, 'customAdminLogin'])->name('adminLoggedin');

Route::get('/admin/forgot/password', function () {
    return view('auth/admin_forgot');
})->name('admin_forgot_screen');
Route::post('/admin/forgot-password/email', [ForgotPasswordController::class, 'forgotpassword'])->name('forgotpass');
Route::get('/admin/forgot-password/verify/{token}', [ForgotPasswordController::class, 'verifypassword'])->name('verifyemail');
Route::post('/admin/forgot-password/reset/{token}', [ForgotPasswordController::class, 'changepassword'])->name('resetpassword');

Route::middleware(['auth', 'adminaccess'])->prefix('admin')->group(function () {

    Route::get('/index', [IndexController::class, 'adminHome'])->name('adminIndex');

    Route::resource('agentmanagers', AgentManagerController::class);
    Route::post('delete-agentmanagers', [AgentManagerController::class, 'destroy']);

    Route::resource('destinations', DestinationController::class);
    Route::post('delete-destination', [DestinationController::class, 'destroy']);

    Route::resource('faqs', FaqController::class);
    Route::post('delete-faqs', [FaqController::class, 'destroy']);

    Route::resource('portals', PortalController::class);
    Route::post('portals-create', [PortalController::class, 'portalsStore'])->name('portalsStore');
    Route::post('delete-portal', [PortalController::class, 'destroy']);

    Route::resource('newsletters', NewsLetterController::class);
    Route::post('delete-newsletter', [NewsLetterController::class, 'destroy']);

    Route::resource('send-newsletters', SendNewsLetterController::class);

    Route::resource('contacts', ContactController::class);

    Route::resource('propertyenquiries', PropertyEnquiriesController::class);

    Route::post('propertymanager-create', [PostProperty::class, 'CreateProperty'])->name('PropertyCreate');
    Route::resource('propertymanager', PropertyManagerController::class);
    Route::post('delete-propertymanager', [PropertyManagerController::class, 'destroy']);

    Route::resource('generalsetting', GeneralSettingController::class);

    Route::resource('accountsetting', AccountSettingController::class);
    Route::resource('banners', BannerController::class);
    Route::post('delete-banner', [BannerController::class, 'destroy']);

    Route::resource('news', NewsController::class);
    Route::post('delete-new', [NewsController::class, 'destroy']);

    Route::resource('templates', TemplatesController::class);
    Route::post('delete-template', [TemplatesController::class, 'destroy']);

    Route::resource('reviewsratings', ReviewsRatingsController::class);
    Route::post('delete-reviewsrating', [ReviewsRatingsController::class, 'destroy']);

    Route::resource('sociallink', SocialLinkController::class);
    Route::post('delete-sociallink', [SocialLinkController::class, 'destroy']);

    Route::resource('smtpmail', SmtpMailController::class);

    Route::resource('subscribed-users', SubscribedusersController::class);

    Route::resource('amenities', AmenitiesController::class);
    Route::post('delete-amenities', [AmenitiesController::class, 'destroy']);

    Route::resource('users', UserManagerController::class);
    Route::post('delete-users', [UserManagerController::class, 'destroy']);

    Route::resource('agents', AgentManagerController::class);
    Route::post('delete-agents', [AgentManagerController::class, 'destroy']);

    Route::resource('sent-to-users', MailSentToController::class);

    Route::resource('currencies', CurrencyManagerController::class);
    Route::post('delete-currencies', [CurrencyManagerController::class, 'destroy']);


    Route::resource('propertytypes', PropertyTypeController::class);
    Route::post('delete-propertytype', [PropertyTypeController::class, 'destroy']);

    Route::get('/PropertyCategory', [PropertyCategoryController::class, 'index'])->name('PropertyCategory');
    Route::get('/propertyCategory/create', [PropertyCategoryController::class, 'create'])->name('propertyCategorycreate');
    Route::get('/propertyCategory/delete/{id}', [PropertyCategoryController::class, 'Delete'])->name('propertyCategorydelete');

    Route::get('/propertyCategory/store', [PropertyCategoryController::class, 'store'])->name('propertyCategorystore');

    Route::get('/flags', [FlagController::class, 'index'])->name('Flags');
    Route::get('/flags/create', [FlagController::class, 'create'])->name('flagcreate');
    Route::post('/flags/store', [FlagController::class, 'store'])->name('flagstore');
    Route::get('delete-flag/{id}', [FlagController::class, 'destroy'])->name('flagdestroy');

    Route::resource('countries', CountryNameController::class);
    Route::post('delete-country', [CountryNameController::class, 'destroy']);

    Route::resource('states', StateController::class);
    Route::post('delete-state', [StateController::class, 'destroy']);

    Route::resource('cities', CityController::class);
    Route::post('delete-city', [CityController::class, 'destroy']);

    Route::get('/change-password', function () {
        return view('admin/pages/accountsetting/chnage_password');
    })->name('chnagepassword');

    Route::put('/profile/chnage-password', [ResetPasswordController::class, 'admin_chanegpassword'])->name('admin_paas_change');
});

/*------------------------------------------
--------------------------------------------
All Agent Routes List
--------------------------------------------
--------------------------------------------*/
Route::get('/agent/forgot/password', function () {
    return view('auth/admin_forgot');
})->name('admin_forgot_screen1');
Route::post('/agent/forgot-password/email', [ForgotPasswordController::class, 'forgotpassword1'])->name('forgotpass1');
Route::get('/agent/forgot-password/verify/{token}', [ForgotPasswordController::class, 'verifypassword1'])->name('verifyemail1');
Route::post('/agent/forgot-password/reset/{token}', [ForgotPasswordController::class, 'changepassword1'])->name('resetpassword1');

Route::get('agent/profile/change-password', function () {
    return view('agent/pages/profile/change_password');
})->name('agent_change_password');
Route::put('/agent/profile/password-chnaged', [ResetPasswordController::class, 'admin_chanegpassword'])->name('agent_paas_change');

Route::get('/agent/register', [AgentRegisterController::class, 'index'])->name('agentRegister');
Route::post('/agent/registerd', [AgentRegisterController::class, 'store'])->name('customRegister');
Route::get('/agent-orgnizer/account/verify/{token}', [AgentRegisterController::class, 'verifyAccount'])->name('user.verify');

Route::get('agent-login', [LoginController::class, 'agentlogin'])->name('agentLogin');
Route::post('/agent', [LoginController::class, 'customAgentLogin'])->name('agentLoggedin');

Route::middleware(['auth', 'agentaccess', 'verifyEmail'])->prefix('agent')->group(function () {
    Route::get('/home', [HomeController::class, 'agentHome'])->name('agentHome');
    Route::get('/profile', [AgentProfileController::class, 'index'])->name('agentProfile');
    Route::get('/profile/edit/{id}', [AgentProfileController::class, 'edit'])->name('agentEdit');
    Route::put('/profile/update/{id}', [AgentProfileController::class, 'update'])->name('agentUpdate');
    Route::resource('/property', AgentPropertyController::class);
    Route::get('/delete-property/{id}', [AgentPropertyController::class, 'propertyDestroy'])->name('propertyDestroy');
    Route::put('/status-update-property/{id}', [AgentPropertyController::class, 'propertyStatus'])->name('propertyStatus');

    Route::get('/agentenquiry', [AgentEnquiryController::class, 'index']);
    Route::get('/enquiry-show/{id}', [AgentEnquiryController::class, 'enquiryShow'])->name('enquiryShow');

    Route::put('/profile/chnage-password', [ResetPasswordController::class, 'admin_chanegpassword'])->name('admin_paas_change1');
});
/*------------------------------------------
--------------------------------------------
All Normal Users Routes List
--------------------------------------------
--------------------------------------------*/


Route::get('/clear-route', function(){
    Artisan::call('route:cache');
    Artisan::call('optimize:clear');
    Artisan::call('cache:clear');

    return 'route-cleared';
} );


Route::get('/user/register', [RegisterController::class, 'index'])->name('userRegister');
Route::post('/user/registered', [RegisterController::class, 'store'])->name('UserRegistered');
Route::get('/user/account/verify/{token}', [RegisterController::class, 'verifyAccount'])->name('VerifyUser');

Route::get('user-login', [LoginController::class, 'userlogin'])->name('userLogin');
Route::post('loggedin', [LoginController::class, 'customUserLogin'])->name('userLoggedin');


Route::get('/user/forgot/password', function () {
    return view('auth/user_forgot');
})->name('user_forgot_screen');
Route::post('/user/forgot-password/email', [ForgotPasswordController::class, 'forgotpassword2'])->name('forgotpass2');
Route::get('/user/forgot-password/verify/{token}', [ForgotPasswordController::class, 'verifypassword2'])->name('verifyemail2');
Route::post('/user/forgot-password/reset/{token}', [ForgotPasswordController::class, 'changepassword2'])->name('resetpassword2');


Route::get('user/profile/change-password', function () {
    return view('user/pages/profile/change_password');
})->name('user_change_password');
Route::put('/user/profile/password-chnaged', [ResetPasswordController::class, 'admin_chanegpassword'])->name('user_paas_change');

Route::get('log-out', [LogoutController::class, 'Logout'])->name('customLogout');


Route::get('/search-buy', [SearchController::class, 'index'])->name('search-buy');
Route::get('/search-rent', [SearchController::class, 'indexRent'])->name('search-rent');
Route::get('/search-value', [SearchController::class, 'indexPera'])->name('search-perameter');
Route::get('/search/detail/{id}', [SearchController::class, 'Detail'])->name('searchDetail');
Route::get('/searchCity', [SearchController::class, 'searchCity'])->name('searchCity');
Route::get('/agent-search', [SearchController::class, 'AgentSearch'])->name('searchagents');

Route::get('/subscribeduserdata', [SubscribeduserController::class, 'store'])->name('subscribeduser.store');


Route::resource('contactus', ContactusController::class);
Route::resource('propertylisting', PropertyListingController::class);
Route::get('propertydetails',  [PropertyListingController::class, 'show']);
Route::post('/add-property', [PostPropertyController::class, 'postproperty'])->name('addproperty');

Route::resource('newsblog', NewsblogController::class);

Route::resource('faq', FrontendFaqController::class);

Route::get('/agent-list', [AgentFrontController::class, 'index'])->name('listagent');
Route::get('/emicalculator', [EmiController::class, 'index'])->name('emicalc');
Route::get('/areaconverter', [AreaConverterController::class, 'index'])->name('areacalc');
Route::get('newsdetail/{id}', 'App\Http\Controllers\Frontend\NewsblogController@newdetail')->name('newsdetail');
Route::get('/searchCity', [SearchController::class, 'searchCity'])->name('searchCity');


Route::get('/wishlist/{id}', [WishlistController::class, 'index'])->name('wishlist');
Route::get('/viewwish-list', [WishlistController::class, 'show'])->name('viewwishlist');


Route::get('/wishlistdelete/{id}', [WishlistController::class, 'wishlistDelete'])->name('wishlistdelete');
Route::get('/user-enquiry', [UserEnquiryController::class, 'userEnquiry'])->name('userEnquiry');

Route::get('/property-map-view/{city}', [SearchController::class, 'MapView'])->name('Mapview');

Route::get('/rented-properties', [SearchController::class, 'RentProperties'])->name('RentProperties');
Route::get('/available-properties', [SearchController::class, 'BuyProperties'])->name('BuyProperties');

Route::get('/new-homes', [PropertyListingController::class, 'NewHomes'])->name('newhomes');

Route::get('/Propertybytype', [PropertyListingController::class, 'PropertyType'])->name('propertytypelisting');

Route::get('/sitemap', [SitemapXmlController::class, 'index'])->name('SiteMap');

Route::middleware(['auth', 'verifyEmailUser'])->group(function () {
    Route::resource('advertiseus', AdvertiseController::class);
});
Route::get('{slug}', [CmsController::class, 'getCmsPage']);
