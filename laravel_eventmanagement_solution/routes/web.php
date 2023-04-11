<?php

use App\Http\Controllers\EventOrganizer\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Super Admin Routes Import
use App\Http\Controllers\SuperAdmin\CmsController;
use App\Http\Controllers\SuperAdmin\BannerController;
use App\Http\Controllers\SuperAdmin\FaqController;
use App\Http\Controllers\SuperAdmin\GeneralSettingController;
use App\Http\Controllers\SuperAdmin\TemplateController;
use App\Http\Controllers\SuperAdmin\SmtpController;
use App\Http\Controllers\SuperAdmin\SocialLinkController;
use App\Http\Controllers\SuperAdmin\ContactEnquiryController;
use App\Http\Controllers\SuperAdmin\CurrencyController;
use App\Http\Controllers\SuperAdmin\FoodStoreController;
use App\Http\Controllers\SuperAdmin\MailsentController;
use App\Http\Controllers\SuperAdmin\ReviewsRatingsController;
use App\Http\Controllers\SuperAdmin\FoodItemsController;
use App\Http\Controllers\SuperAdmin\EventController;
use App\Http\Controllers\SuperAdmin\EventCategoryController;
use App\Http\Controllers\SuperAdmin\EventOrganizerController;
use App\Http\Controllers\SuperAdmin\EventTicketController;
use App\Http\Controllers\SuperAdmin\EventTicketPriceController;
use App\Http\Controllers\SuperAdmin\EventManageController;
use App\Http\Controllers\SuperAdmin\ExpertizeController;
use App\Http\Controllers\SuperAdmin\ArtistsController;
use App\Http\Controllers\SuperAdmin\AdminProfileController;
use App\Http\Controllers\SuperAdmin\UnverifiedEvents;
use App\Http\Controllers\SuperAdmin\EventEditController;
// Super Admin Routes Import End


// Event Orag. Routes Import

use App\Http\Controllers\EventOrganizer\OrgnizerController;
use App\Http\Controllers\SuperAdmin\TicketCategoryController;
use App\Http\Controllers\EventOrganizer\OrganizerArtistController;
use App\Http\Controllers\EventOrganizer\OrganizerArtistExpertizeController;
use App\Http\Controllers\EventOrganizer\OrganizerFoodStoreController;
use App\Http\Controllers\Frontend\FaqsController;
use App\Http\Controllers\SuperAdmin\AdminDashboardController;
// End Event Orag. Routes Import


// User Routes Import
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\FrontArtistController;
use App\Http\Controllers\User\FrontEventController;
use App\Http\Controllers\User\FavouriteArtistController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\BuyController;
use App\Http\Controllers\User\CmsFrontController;
use App\Http\Controllers\User\RazorpayController;
use App\Models\TicketListing;
use GuzzleHttp\Middleware;

// User Routes Import End


// User Routes 
Route::get('/', [HomeController::class, 'header'])->name('home');

Route::get('/admin-profile', [AdminProfileController::class, 'index'])->name('AdminProfile');
Route::put('/update-admin-profile/{id}', [AdminProfileController::class, 'updateprofile'])->name('UpdateAdminProfile');
Route::get('/logout-admin-profile', [AdminProfileController::class, 'destroy'])->name('AdminLogout');



Route::get('razorpay', [RazorpayController::class, 'razorpay'])->name('razorpay');
Route::any('/razorpaypayment', [RazorpayController::class, 'payment'])->name('payment');
Route::post('checkout-payment', [FrontEventController::class, 'viewLoad'])->name('checkoutPayment');
Route::get('/cancel-ticekt/{id}', [RazorpayController::class, 'cancelTicekt'])->name('cancelTicket');

Route::get('/popup', function () {
    return view('popup');
});

Route::get('/view-artist', [FrontArtistController::class, 'index'])->name('ArtistShow');
Route::get('/show-artist/{id}', [FrontArtistController::class, 'show'])->name('ArtistShowDetail');

Route::get('/view-event', [FrontEventController::class, 'index'])->name('EventShow');
Route::get('/show-event/{id}', [FrontEventController::class, 'show'])->name('EventShowDetail');
Route::get('/food-store-detail/{id}', [FrontEventController::class, 'FoodStoreDetail'])->name('FoodStoreDetail');
Route::get('/food-store-detail/{id}', [FrontEventController::class, 'FoodStoreDetail'])->name('FoodStoreDetail');
Route::post('/add-ticket', [FrontEventController::class, 'add'])->name('addTicket');
Route::get('/user-faqs', [FaqsController::class, 'index'])->name('FaqDetails');

Route::get('/buy-event', [BuyController::class, 'buy'])->name('eventsBuy');
Route::get('/ticket-confirmation', [BuyController::class, 'TicketConfirmation'])->name('TicketConfirmation');


Route::get('/favourite/{id}', [FavouriteArtistController::class, 'index'])->name('FavoutiteArtist');
Route::get('/view-favourite-artist', [FavouriteArtistController::class, 'ViewFavouriteArtist'])->name('ViewFavouriteArtist');


Route::get('/search', [HomeController::class, 'search'])->name('Search');


Route::get('/user-profile', [ProfileController::class, 'index'])->middleware('verified')->name('UserProfile');
Route::get('/edit-user-profile/{id}', [ProfileController::class, 'edit'])->name('EditUserProfile');
Route::put('/update-user-profile/{id}', [ProfileController::class, 'update'])->name('UpdateUserProfile');
Route::post('/changing-user-password/{id}', [ProfileController::class, 'changingPassword'])->name('PasswordChange');
Route::view('update-user-password', 'User.Pages.Profile.ChangePassword')->name('ChangePassword');
Route::get('ticket-listing', [ProfileController::class, 'ticketListing'])->name('TicketListing');
Route::get('ticket-listing-detail/{id}', [ProfileController::class, 'ticketListingDetail'])->name('TicketListingDetail');

// User Routes End

// Event Org Routes
Route::get('/event-orgnizer/register', function () {
    return view('EventOrgnizer/Auth/signup');
})->name('orgnizerregister');
Route::post('/event-orgnizer/registered', [OrgnizerController::class, 'postRegistration'])->name('orgregister');
Route::get('/event-orgnizer/account/verify/{token}', [OrgnizerController::class, 'verifyAccount'])->name('user.verify');

Route::get('/event-orgnizer/login', [OrgnizerController::class, 'loginCheck'])->name('EventOrg');
Route::post('/event-orgnizer-loggedin', [OrgnizerController::class, 'loginEventOrgnizer'])->name('eventOrgnizerLogin');
Route::get('/event-orgnizer/logout', [OrgnizerController::class, 'logoutOrg'])->name('logoutOrg1');

Route::get('/event-orgnizer/forgot-password', function () {
    return view('EventOrgnizer/Auth/forgotpassword');
})->name('forgotpassword');
Route::post('/event-orgnizer/forgot-password/email', [OrgnizerController::class, 'forgotpassword'])->name('forgotpass');
Route::get('/event-orgnizer/forgot-password/verify/{token}', [OrgnizerController::class, 'verifypassword'])->name('verifyemail');
Route::post('/event-orgnizer/forgot-password/reset/{token}', [OrgnizerController::class, 'changepassword'])->name('resetpassword');

Route::middleware(['auth', 'verify_email', 'verify_orgnizer'])->prefix('event-orgnizer')->group(function () {
    Route::get('/index', [DashboardController::class, 'index'])->name('orgnizerdashboard');

   
    Route::get('/events/list', [OrgnizerController::class, 'index'])->name('listevents');
    Route::get('/create/events', [OrgnizerController::class, 'create'])->name('createevents');
    Route::post('/store/event', [OrgnizerController::class, 'store'])->name('storeevent');
    Route::get('/event/delete/{id}', [OrgnizerController::class, 'delete'])->name('eventdelete');
    Route::get('/event/edit/{id}', [OrgnizerController::class, 'edit'])->name('eventedit');
    Route::put('/event/update/{id}', [OrgnizerController::class, 'update'])->name('updateevent');
    Route::get('/event/show/{id}', [OrgnizerController::class, 'show'])->name('showevent');
    Route::get('/profile', [OrgnizerController::class, 'profile'])->name('profileorg');
    Route::get('/profile-image-delete', [OrgnizerController::class, 'profileImageDelete'])->name('deleteprofileimage');

    Route::put('/profile-update', [OrgnizerController::class, 'profileupdate'])->name('updateprofile');

    Route::put('profile/chnage-password', [OrgnizerController::class, 'chanegpassword'])->name('changepassword');

    Route::get('/ticket-show-organizer/{id}', [OrgnizerController::class, 'ticketShow'])->name('TicketShow');


    Route::get('/delete/event/foodstore/{id}', [OrgnizerController::class, 'delete_foodstore'])->name('food_store_delete1');

    Route::get('/delete/event/artist/{id}', [OrgnizerController::class, 'delete_artist'])->name('artist_delete1');

    Route::get('/delete/event/ticket/{id}', [OrgnizerController::class, 'delete_ticket'])->name('ticket_delete1');

    Route::get('/expertize-organizer', [OrganizerArtistExpertizeController::class, 'index'])->name('OrganizerArtistExpertize');
    Route::get('/create-artist-organizer-expertize', [OrganizerArtistExpertizeController::class, 'create'])->name('CreateOrganizerArtistExpertize');
    Route::post('/store-artist-organizer-expertize', [OrganizerArtistExpertizeController::class, 'store'])->name('StoreOrganizerArtistExpertize');
    Route::get('/edit-artist-organizer-expertize/{id}', [OrganizerArtistExpertizeController::class, 'edit'])->name('EditOrganizerArtistExpertize');
    Route::put('/update-artist-organizer-expertize/{id}', [OrganizerArtistExpertizeController::class, 'update'])->name('UpdateOrganizerArtistExpertize');
    Route::get('/show-artist-organizer-expertize/{id}', [OrganizerArtistExpertizeController::class, 'show'])->name('ShowOrganizerArtistExpertize');
    Route::get('/delete-artist-organizer-expertize/{id}', [OrganizerArtistExpertizeController::class, 'delete'])->name('DeleteOrganizerArtistExpertize');

    Route::get('/artists-organizer', [OrganizerArtistController::class, 'index'])->name('ArtistsOrganizer');
    Route::get('/create-artist-organizer', [OrganizerArtistController::class, 'create'])->name('CreateArtistsOrganizer');
    Route::post('/store-artist-organizer', [OrganizerArtistController::class, 'store'])->name('StoreArtistOrganizer');
    Route::get('/edit-artist-organizer/{id}', [OrganizerArtistController::class, 'edit'])->name('EditArtistOrganizer');
    Route::put('/update-artist-organizer/{id}', [OrganizerArtistController::class, 'update'])->name('UpdateArtistOrganizer');
    Route::get('/delete-artist-organizer/{id}', [OrganizerArtistController::class, 'delete'])->name('DeleteArtistOrganizer');
    Route::get('/show-artists-organizer/{id}', [OrganizerArtistController::class, 'show'])->name('ShowArtistOrganizer');


    Route::get('/food-store', [OrganizerFoodStoreController::class, 'index'])->name('FoodStoresOrganizer');
    Route::get('/create-food-store', [OrganizerFoodStoreController::class, 'create'])->name('CreateFoodStoresOrganizer');
    Route::post('/store-food-store', [OrganizerFoodStoreController::class, 'store'])->name('StoreFoodStoreOrganizer');
    Route::get('/edit-food-store/{id}', [OrganizerFoodStoreController::class, 'edit'])->name('EditFoodStoreOrganizer');
    Route::put('/update-food-store/{id}', [OrganizerFoodStoreController::class, 'update'])->name('UpdateFoodStoreOrganizer');
    Route::get('/show-food-store/{id}', [OrganizerFoodStoreController::class, 'show'])->name('ShowFoodStoreOrganizer');
    Route::get('/delete-food-store/{id}', [OrganizerFoodStoreController::class, 'delete'])->name('DeleteFoodStoreOrganizer');
    Route::get('/delete/food-menu/{id}', [OrganizerFoodStoreController::class, 'FoodMenuDelete'])->name('DeleteFoodMenu');


});
// End Event Org Routes

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'user-access'])->prefix('super-admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('superAdmin');

    Route::get('/cms', [CmsController::class, 'index'])->name('Cms');
    Route::get('/create-cms', [CmsController::class, 'create'])->name('CreateCms');
    Route::post('/store-cms', [CmsController::class, 'store'])->name('StoreCms');
    Route::get('/edit-cms/{id}', [CmsController::class, 'edit'])->name('EditCms');
    Route::put('/update-cms/{id}', [CmsController::class, 'update'])->name('UpdateCms');
    Route::get('/show-cms/{id}', [CmsController::class, 'show'])->name('ShowCms');
    Route::get('delete-cms/{id}', [CmsController::class, 'destroy'])->name('DeleteCms');


    Route::get('/banners', [BannerController::class, 'index'])->name('Banners');
    Route::get('/create-banner', [BannerController::class, 'create'])->name('CreateBanner');
    Route::post('/store-banner', [BannerController::class, 'store'])->name('StoreBanner');
    Route::get('/edit-banner/{id}', [BannerController::class, 'edit'])->name('EditBanner');
    Route::put('/update-banner/{id}', [BannerController::class, 'update'])->name('UpdateBanner');
    Route::get('/show-banner/{id}', [BannerController::class, 'show'])->name('ShowBanner');
    Route::get('delete-banner/{id}', [BannerController::class, 'destroy'])->name('flagdestroy');


    Route::get('/faqs', [FaqController::class, 'index'])->name('Faqs');
    Route::get('/create-faq', [FaqController::class, 'create'])->name('CreateFaq');
    Route::post('/store-faq', [FaqController::class, 'store'])->name('StoreFaq');
    Route::get('/edit-faq/{id}', [FaqController::class, 'edit'])->name('EditFaq');
    Route::put('/update-faq/{id}', [FaqController::class, 'update'])->name('UpdateFaq');
    Route::get('/show-faq/{id}', [FaqController::class, 'show'])->name('ShowFaq');
    Route::get('/delete-faq/{id}', [FaqController::class, 'delete'])->name('DeleteFaq');

    Route::get('/templates', [TemplateController::class, 'index'])->name('Templates');
    Route::get('/create-template', [TemplateController::class, 'create'])->name('CreateTemplate');
    Route::post('/store-template', [TemplateController::class, 'store'])->name('StoreTemplate');
    Route::get('/edit-template/{id}', [TemplateController::class, 'edit'])->name('EditTemplate');
    Route::put('/update-template/{id}', [TemplateController::class, 'update'])->name('UpdateTemplate');
    Route::get('/show-template/{id}', [TemplateController::class, 'show'])->name('ShowTemplate');
    Route::get('/delete-template/{id}', [TemplateController::class, 'delete'])->name('DeleteTemplate');

    Route::get('/generalsettings', [GeneralSettingController::class, 'index'])->name('GeneralSetting');
    Route::get('/edit-generalsettings/{id?}', [GeneralSettingController::class, 'edit'])->name('EditGeneralSetting');
    Route::put('/update-generalsettings/{id?}', [GeneralSettingController::class, 'update'])->name('UpdateGeneralSetting');

    Route::get('/smtp-mail', [SmtpController::class, 'index'])->name('SmtpMail');
    Route::get('/edit-smtp-mail/{id}', [SmtpController::class, 'edit'])->name('EditSmtp');
    Route::put('/update-smtp-mail/{id}', [SmtpController::class, 'update'])->name('UpdateSmtp');

    Route::get('/social-links', [SocialLinkController::class, 'index'])->name('SocialLinks');
    Route::get('/create-social-links', [SocialLinkController::class, 'create'])->name('CreateSocialLink');
    Route::post('/store-social-links', [SocialLinkController::class, 'store'])->name('StoreSocialLinks');
    Route::get('/edit-social-links/{id}', [SocialLinkController::class, 'edit'])->name('EditSocialLink');
    Route::put('/update-social-links/{id}', [SocialLinkController::class, 'update'])->name('UpdateSocialLink');
    Route::get('/delete-social-links/{id}', [SocialLinkController::class, 'delete'])->name('DeleteSocialLink');

    Route::get('/contact-enquiry', [ContactEnquiryController::class, 'index'])->name('ContactEnquiry');
    Route::get('/show-contact-enquiry/{id}', [ContactEnquiryController::class, 'show'])->name('ShowContactEnquiry');

    Route::get('/mail-sent-users', [MailsentController::class, 'index'])->name('MailSentUser');

    Route::get('/reviews-ratings', [ReviewsRatingsController::class, 'index'])->name('ReviewsRatings');
    Route::get('/create-reviews-ratings', [ReviewsRatingsController::class, 'create'])->name('CreateReviewsRatings');
    Route::post('/store-reviews-ratings', [ReviewsRatingsController::class, 'store'])->name('StoreReviewsRatings');
    Route::get('/edit-reviews-ratings/{id}', [ReviewsRatingsController::class, 'edit'])->name('EditReviewsRatings');
    Route::put('/update-reviews-ratings/{id}', [ReviewsRatingsController::class, 'update'])->name('UpdateReviewsRatings');
    Route::get('/show-reviews-ratings/{id}', [ReviewsRatingsController::class, 'show'])->name('ShowReviewsRatings');
    Route::get('/delete-reviews-ratings/{id}', [ReviewsRatingsController::class, 'delete'])->name('DeleteReviewsRatings');

    Route::get('/food-stores', [FoodStoreController::class, 'index'])->name('FoodStores');
    Route::get('/create-food-stores', [FoodStoreController::class, 'create'])->name('CreateFoodStores');
    Route::post('/store-food-stores', [FoodStoreController::class, 'store'])->name('StoreFoodStore');
    Route::get('/edit-food-stores/{id}', [FoodStoreController::class, 'edit'])->name('EditFoodStore');
    Route::put('/update-food-stores/{id}', [FoodStoreController::class, 'update'])->name('UpdateFoodStore');
    Route::get('/show-food-stores/{id}', [FoodStoreController::class, 'show'])->name('ShowFoodStore');
    Route::get('/delete-food-stores/{id}', [FoodStoreController::class, 'delete'])->name('DeleteFoodStore');

    Route::get('/food-items', [FoodItemsController::class, 'index'])->name('FoodItems');
    Route::get('/create-food-items', [FoodItemsController::class, 'create'])->name('CreateFoodItems');
    Route::post('/store-food-items', [FoodItemsController::class, 'store'])->name('StoreFoodItems');
    Route::get('/edit-food-items/{id}', [FoodItemsController::class, 'edit'])->name('EditFoodItems');
    Route::put('/update-food-items/{id}', [FoodItemsController::class, 'update'])->name('UpdateFoodItems');
    Route::get('/show-food-items/{id}', [FoodItemsController::class, 'show'])->name('ShowFoodItems');
    Route::get('/delete-food-items/{id}', [FoodItemsController::class, 'delete'])->name('DeleteFoodItems');

    Route::get('/currency', [CurrencyController::class, 'index'])->name('Currency');
    Route::get('/create-currency', [CurrencyController::class, 'create'])->name('CreateCurrency');
    Route::post('/store-currency', [CurrencyController::class, 'store'])->name('StoreCurrency');
    Route::get('/edit-currency/{id}', [CurrencyController::class, 'edit'])->name('EditCurrency');
    Route::put('/update-currency/{id}', [CurrencyController::class, 'update'])->name('UpdateCurrency');
    Route::get('/show-currency/{id}', [CurrencyController::class, 'show'])->name('ShowCurrency');
    Route::get('/delete-currency/{id}', [CurrencyController::class, 'delete'])->name('DeleteCurrency');

    Route::get('/event-category', [EventCategoryController::class, 'index'])->name('EventCategory');
    Route::get('/create-event-category', [EventCategoryController::class, 'create'])->name('CreateEventCategory');
    Route::post('/store-event-category', [EventCategoryController::class, 'store'])->name('StoreEventCategory');
    Route::get('/show-event-category/{id}', [EventCategoryController::class, 'show'])->name('ShowEventCategory');
    Route::get('/edit-event-category/{id}', [EventCategoryController::class, 'edit'])->name('EditEventCategory');
    Route::put('/update-event-category/{id}', [EventCategoryController::class, 'update'])->name('UpdateEventCategory');
    Route::get('/delete-event-category/{id}', [EventCategoryController::class, 'delete'])->name('DeleteEventCategory');


    Route::get('/event', [EventController::class, 'index'])->name('Events');
    Route::get('/create-event', [EventController::class, 'create'])->name('CreateEvents');
    Route::post('/store-event', [EventController::class, 'store'])->name('StoreEvents');
    Route::get('/show-event-list/{id}', [EventController::class, 'show'])->name('ShowEvents');
    Route::get('/edit-event/{id}', [EventController::class, 'edit'])->name('EditEvents');
    Route::get('/eventlist-delete-event/{id}', [EventController::class, 'delete'])->name('DeleteEvents');
    Route::put('/update-event/{id}', [EventController::class, 'update'])->name('UpdateEvents');
    Route::get('/ticket-show-admin/{id}', [EventController::class, 'ticketShowEvent'])->name('TicketShowEvents');




    Route::get('/event-manage', [EventManageController::class, 'index'])->name('EventManage');
    Route::get('/create-event-manage', [EventManageController::class, 'create'])->name('CreateEventManage');
    Route::post('/store-event-manage', [EventManageController::class, 'store'])->name('StoreEventManage');
    Route::get('/show-event-manage/{id}', [EventManageController::class, 'show'])->name('ShowEventManage');
    Route::get('/edit-event-manage/{id}', [EventManageController::class, 'edit'])->name('EditEventManage');
    Route::put('/update-event-manage/{id}', [EventManageController::class, 'update'])->name('UpdateEventManage');
    Route::get('/delete-event-manage/{id}', [EventManageController::class, 'delete'])->name('DeleteEventManage');
    Route::get('/price-event-manage', [EventManageController::class, 'ManagePrice'])->name('PriceEventManage');


    Route::get('/ticket-category', [TicketCategoryController::class, 'index'])->name('TicketCategory');
    Route::get('/create-ticket-category', [TicketCategoryController::class, 'create'])->name('CreateTicketCategory');
    Route::post('/store-ticket-category', [TicketCategoryController::class, 'store'])->name('StoreTicketCategory');
    Route::get('/show-ticket-category/{id}', [TicketCategoryController::class, 'show'])->name('ShowTicketCategory');
    Route::get('/edit-ticket-category/{id}', [TicketCategoryController::class, 'edit'])->name('EditTicketCategory');
    Route::put('/update-ticket-category/{id}', [TicketCategoryController::class, 'update'])->name('UpdateTicketCategory');
    Route::get('/delete-ticket-category/{id}', [TicketCategoryController::class, 'delete'])->name('DeleteTicketCategory');


    Route::get('/event-organizer', [EventOrganizerController::class, 'index'])->name('EventOrganizer');
    Route::get('/create-event-organizer', [EventOrganizerController::class, 'create'])->name('CreateEventOrganizer');
    Route::post('/store-event-organizer', [EventOrganizerController::class, 'store'])->name('StoreEventOrganizer');
    Route::get('/show-event-organizer/{id}', [EventOrganizerController::class, 'show'])->name('ShowEventOrganizer');
    Route::get('/edit-event-organizer/{id}', [EventOrganizerController::class, 'edit'])->name('EditEventOrganizer');
    Route::put('/update-event-organizer/{id}', [EventOrganizerController::class, 'update'])->name('UpdateEventOrganizer');
    Route::get('/delete-event-organizer/{id}', [EventOrganizerController::class, 'delete'])->name('DeleteEventOrganizer');



    Route::get('/event-ticket', [EventTicketController::class, 'index'])->name('EventTicket');
    Route::get('/create-event-ticket', [EventTicketController::class, 'create'])->name('CreateEventTicket');
    Route::post('/store-event-ticket', [EventTicketController::class, 'store'])->name('StoreEventTicket');
    Route::get('/show-event-ticket/{id}', [EventTicketController::class, 'show'])->name('ShowEventTicket');
    Route::get('/edit-event-ticket/{id}', [EventTicketController::class, 'edit'])->name('EditEventTicket');
    Route::put('/update-event-ticket/{id}', [EventTicketController::class, 'update'])->name('UpdateEventTicket');
    Route::get('/delete-event-ticket/{id}', [EventTicketController::class, 'delete'])->name('DeleteEventTicket');


    Route::get('/event-ticket-price', [EventTicketPriceController::class, 'index'])->name('EventTicketPrice');
    Route::get('/create-event-ticket-price', [EventTicketPriceController::class, 'create'])->name('CreateEventTicketPrice');
    Route::post('/store-event-ticket-price', [EventTicketPriceController::class, 'store'])->name('StoreEventTicketPrice');
    Route::get('/show-event-ticket-price/{id}', [EventTicketPriceController::class, 'show'])->name('ShowEventTicketPrice');
    Route::get('/edit-event-ticket-price/{id}', [EventTicketPriceController::class, 'edit'])->name('EditEventTicketPrice');
    Route::put('/update-event-ticket-price/{id}', [EventTicketPriceController::class, 'update'])->name('UpdateEventTicketPrice');
    Route::get('/delete-event-ticket-price/{id}', [EventTicketPriceController::class, 'delete'])->name('DeleteEventTicketPrice');


    Route::get('/artist-expertize', [ExpertizeController::class, 'index'])->name('ArtistExpertize');
    Route::get('/create-artist-expertize', [ExpertizeController::class, 'create'])->name('CreateArtistExpertize');
    Route::post('/store-artist-expertize', [ExpertizeController::class, 'store'])->name('StoreArtistExpertize');
    Route::get('/edit-artist-expertize/{id}', [ExpertizeController::class, 'edit'])->name('EditArtistExpertize');
    Route::put('/update-artist-expertize/{id}', [ExpertizeController::class, 'update'])->name('UpdateArtistExpertize');
    Route::get('/show-artist-expertize/{id}', [ExpertizeController::class, 'show'])->name('ShowArtistExpertize');
    Route::get('/delete-artist-expertize/{id}', [ExpertizeController::class, 'delete'])->name('DeleteArtistExpertize');

    Route::get('/artists', [ArtistsController::class, 'index'])->name('Artists');
    Route::get('/create-artist', [ArtistsController::class, 'create'])->name('CreateArtists');
    Route::post('/store-artist', [ArtistsController::class, 'store'])->name('StoreArtist');
    Route::get('/edit-artist/{id}', [ArtistsController::class, 'edit'])->name('EditArtist');
    Route::put('/update-artist/{id}', [ArtistsController::class, 'update'])->name('UpdateArtist');
    Route::get('/delete-artist/{id}', [ArtistsController::class, 'delete'])->name('DeleteArtist');
    Route::get('/show-artists/{id}', [ArtistsController::class, 'show'])->name('ShowArtist');

    Route::get('/un-verified-events', [UnverifiedEvents::class, 'index'])->name('UnVerifiedEvents');
    Route::get('/verified-event/{id}', [UnverifiedEvents::class, 'verify'])->name('verify_event');
    Route::get('/show-event/{id}', [UnverifiedEvents::class, 'show'])->name('show_event');
    Route::get('/delete-event/{id}', [UnverifiedEvents::class, 'delete'])->name('delete_event');
    Route::get('/un-verify-event/{id}', [UnverifiedEvents::class, 'unverify'])->name('Un_Verify');

    Route::get('/delete/event/foodstore/{id}', [EventEditController::class, 'delete_foodstore'])->name('food_store_delete');

    Route::get('/delete/event/artist/{id}', [EventEditController::class, 'delete_artist'])->name('artist_delete');

    Route::get('/delete/event/ticket/{id}', [EventEditController::class, 'delete_ticket'])->name('ticket_delete');

    Route::put('/update/event/{id}', [EventEditController::class, 'event_update'])->name('update_events');

    Route::get('/delete/event-menu/{id}', [EventEditController::class, 'delete_menu'])->name('delete_food_menu');
});
// Super Admin Routes End


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';

Route::get('{slug}', [CmsFrontController::class, 'getCmsPage']);
