<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {

  Route::group(['namespace' => 'Auth'], function () {
    Route::post('check/email', 'AppRegisterController@checkEmail');
    //Route::post('registre', ['as' => 'app.register.new', 'uses' => 'AppRegisterController@registerCustNew']);
    Route::post('registr', ['as' => 'app.register', 'uses' => 'AppRegisterController@registerCustCustom']);
    Route::post('otp/generate', 'AppRegisterController@generateOtp');
    Route::post('otp/verify', 'AppRegisterController@verifyOtp');
    Route::post('registration', 'AppRegisterController@registerCust');
    Route::post('login', 'AppLoginController@authenticate');
    Route::post('socialLogin', 'AppLoginController@socialLogin');
    Route::post('logout', 'AppLoginController@logout');

    Route::post('merchant/login', 'MerchantLoginController@authenticate');

    Route::post('forgot/password', 'AppForgotPasswordController@checkMail');
    Route::post('forgot/password/otp', 'AppForgotPasswordController@checkOTP');
    Route::post('forgot/password/update', 'AppForgotPasswordController@updatePassword');

    Route::post('forgot/password', 'AppForgotPasswordController@checkMail');
    Route::post('forgot/password/otp', 'AppForgotPasswordController@checkOTP');
    Route::post('forgot/password/update', 'AppForgotPasswordController@updatePassword');

  });
  Route::group(['namespace' => 'Api'], function () {
    Route::get('get/nations', 'ProfileController@getNations');
    Route::get('get/residence', 'ProfileController@getResidence');
    Route::get('get/contactus', 'ProfileController@getContact');


    Route::post('verify/fin', 'ProfileController@verify');

    Route::get('pages/list', 'PagesController@listout');
    Route::post('pages/view', 'PagesController@view');

    Route::get('emergency/list', 'EmergencyController@listout');
    Route::get('taxi/list', 'EmergencyController@taxiList');

    Route::get('dormitory/dormitory_list', 'DormitoryController@dormitory_list');

    Route::post('flexm/create/merchant',            'FlexmTopupController@createMerchant');
    Route::post('flexm/create/terminal',            'FlexmTopupController@createTerminal');
    Route::post('flexm/merchant/info',              'FlexmTopupController@getMerchant');
    
    Route::post('home', 'ProfileController@home');
    
    Route::group(['middleware' => ['jwt-auth', 'user-blocked']], function () {
        Route::post('notify', 'ProfileController@sendSingle');
        //menu_list
        Route::get('menu/list', 'MenuController@getList');
        Route::get('menu/listing', 'MenuController@getListNew');
        // //singx
        // Route::group(['prefix' => 'singx'], function () {
        //
        //     Route::post('login',              'SingxController@login');
        //
        //     Route::post('bank/list',          'SingxController@bankList');
        //
        //     Route::post('exchange/rate',      'SingxController@getExchange');
        //
        //
        //     //sender
        //     Route::post('sender/add',          'SingxController@addSender');
        //     Route::post('sender/list',         'SingxController@listSender');
        //     Route::post('sender/update',       'SingxController@updateSender');
        //
        //     //receiver
        //     Route::post('receiver/add',        'SingxController@addReceiver');//generate otp
        //     Route::post('receiver/list',       'SingxController@listReceiver');
        //     Route::post('receiver/update',     'SingxController@updateReceiver');
        //
        //     Route::post('search/ifsc',        'SingxController@serachIfsc');
        //
        //     //transaction
        //     Route::post('transaction/purpose',         'SingxController@listPurpose');
        //     Route::post('transaction/history',         'SingxController@listHistory');
        //
        //     Route::post('transaction/create',  'SingxController@createTxn');//generate otp
        //     Route::post('sender/update',       'SingxController@updateSender');
        //
        // });

        Route::post('wifi/login',              'FlexmController@kiwire');

        //flexm
        Route::group(['prefix' => 'flexm', 'middleware' => 'flexm-response'], function () {
            Route::post('getBalance',                 'FlexmTopupController@getBalance');
            Route::post('scan',                       'FlexmTopupController@scanQR');

            Route::post('country/list',               'FlexmTopupController@getCountryList');

            Route::post('options',              'FlexmController@options');
            Route::post('register',              'FlexmController@register');
            Route::post('login',              'FlexmController@login');
            Route::post('logout',              'FlexmController@logout');

            Route::post('profile/view',              'FlexmController@getProfile');
            Route::post('profile/update',              'FlexmController@updateProfile');
            Route::post('profile/upload/image',      'FlexmController@uploadPhoto');
            Route::post('profile/change/mobile',      'FlexmController@updateMobileNumber');
            Route::post('profile/address',              'FlexmController@updateAddress');

            Route::post('referral/view',              'FlexmController@getReferralCode');

            Route::post('password/forgot',     'FlexmController@forgotPassword');
            Route::post('password/reset',      'FlexmController@resetPassword');
            Route::post('password/change',      'FlexmController@changePassword');

            Route::post('generate/otp',      'FlexmController@generateOTP');
            Route::post('verify/otp',      'FlexmController@verifyOTP');

            Route::post('doc/upload',      'FlexmController@uploadDoc');
            Route::post('doc/submit',      'FlexmController@submitDoc');

            Route::post('transaction/history',          'FlexmTopupController@getTxnsHistory');
            Route::post('transaction/recordByID',       'FlexmTopupController@paymentRecordByID');

            Route::post('provider/list',              'FlexmTopupController@listProvider');
            Route::post('provider/create',              'FlexmTopupController@createProvider');

            Route::post('card/types',              'FlexmTopupController@cardType');
            Route::post('card/create',              'FlexmTopupController@createCard');
            Route::post('card/verify',              'FlexmTopupController@cardOTP');
            Route::post('card/list',              'FlexmTopupController@listCard');
            Route::post('card/viewById',          'FlexmTopupController@listCardInfo');
            Route::post('card/saved',             'FlexmTopupController@listSavedCardInfo');
            Route::post('card/cvv',              'FlexmTopupController@retrieveCVV');
            Route::post('card/suspend',              'FlexmTopupController@suspendCard');
            Route::post('card/history',              'FlexmTopupController@hisotryCard');

            Route::post('transfer/walletToCard',              'FlexmTopupController@walletToCard');
            Route::post('transfer/cardToWallet',              'FlexmTopupController@cardToWallet');
            Route::post('transfer/walletToWallet',            'FlexmTopupController@walletToWallet');

            Route::post('remittance/providers',            'FlexmTopupController@remittanceProviders');
            Route::post('remittance/corridors',            'FlexmTopupController@remittanceCorridors');
            Route::post('remittance/calculate',            'FlexmTopupController@remittanceCalculate');
            Route::post('remittance/purpose',            'FlexmTopupController@remittancePurpose');
            Route::post('remittance/incomeSource',            'FlexmTopupController@remittanceIncSource');
            Route::post('remittance/branchList',        'FlexmTopupController@branchList');
            Route::post('remittance/create',            'FlexmTopupController@remittanceCreate');
            Route::post('remittance/confirm',            'FlexmTopupController@remittanceConfirm');
            Route::post('remittance/merchantToken',            'FlexmTopupController@generateMerchantToken');
            Route::post('remittance/merchantInfo',            'FlexmTopupController@getMerchantInfo');
            Route::post('remittance/authorization',            'FlexmTopupController@paymentAuthorization');


            Route::post('payment/create',            'FlexmTopupController@makePayment');
            Route::post('payment/qr',                'FlexmTopupController@makePaymentQR');
        });

        //singx
        Route::group(['prefix' => 'singx'], function () {

            Route::post('signup',             'SingxController@signup');
            Route::post('verify',             'SingxController@checkStatus');
            Route::post('login',              'SingxController@login');
            Route::post('forgotPassword',     'SingxController@forgotPassword');
            Route::post('post/login',         'SingxController@postLogin');
            Route::post('user/detail',        'SingxController@getProfile');

            Route::post('bank/list',          'SingxController@getListByCountryNWired');
            Route::post('bank/findByCountryId',          'SingxController@getListByCountry');
            Route::post('list/branch',          'SingxController@getBranchListByBank');

            Route::post('exchange/rate',      'SingxController@getExchange');

            //sender
            Route::post('sender/list',         'SingxController@listSender');
            Route::post('sender/add',          'SingxController@addSender');
            Route::post('sender/view',       'SingxController@getView');
            Route::post('country/list',       'SingxController@getCountryList');
            
            //receiver
            Route::post('receiver/list',       'SingxController@listReceiver');
            Route::post('receiver/view',       'SingxController@viewReceiver');
            Route::post('receiver/findById',       'SingxController@getReceiverById');
            Route::post('receiver/add',        'SingxController@addReceiver');//generate otp
            Route::post('receiver/update',     'SingxController@updateReceiverAccount');
            Route::post('receiver/otp',      'SingxController@generateOTP');

            Route::post('receiver/account/types',       'SingxController@getAccountType');

            Route::post('search/ifsc',        'SingxController@searchIfsc');
            
            Route::post('relationship/list',         'SingxController@listRelationship');
            //transaction
            Route::post('transaction/purpose',         'SingxController@listPurpose');
            Route::post('transaction/history',         'SingxController@listHistory');

            Route::post('update/enquiry',       'SingxController@udpateEnquiry');
            Route::post('transaction/otp',      'SingxController@generateOtpTxn');
            Route::post('transaction/receiver',  'SingxController@updateReceiver');

            Route::post('transaction/create',    'SingxController@createTransaction');
            Route::post('transaction/list',    'SingxController@listTransaction');


        });

        //spuul
        Route::group(['prefix' => 'spuul', 'middleware' => ['spuul-auth']], function () {

            Route::post('token',                 'SpuulController@getToken');
            Route::post('category',              'SpuulController@picks');
            Route::post('category/id',           'SpuulController@pickById');

            Route::post('carousels',             'SpuulController@getCarousels');
            Route::post('search',                'SpuulController@search');

            Route::post('profile',               'SpuulController@profile');
            Route::post('register',              'SpuulController@register');
            Route::post('subscription',          'SpuulController@subscription');
            Route::post('unsubscribe',           'SpuulController@unsubscribe');
            Route::post('subscription/cancel',   'SpuulController@cancelSubscription');
            Route::post('subscribe',             'SpuulController@subscribe');
            Route::post('plans',                 'SpuulController@planList');

            Route::post('browse',                'SpuulController@browse');
            Route::post('video',                 'SpuulController@getDetail');
            Route::post('forgot/password',       'SpuulController@forgot');

        });



        //menu_count
        Route::post('menu/count', 'MenuController@addCount');
        //ad count
        Route::post('impression/add', 'ProfileController@addImpression');
        Route::post('get/popupAd', 'ProfileController@getPopupAd');

        //profile
        Route::post('profile', 'ProfileController@profile');
        Route::post('profile/save', 'ProfileController@save');

        //search keyword
        Route::post('search/add', 'ActivityController@add');

        //mom
        Route::post('mom/options',   'ServicesController@list_feedback_mom_category');
        Route::post('mom/category',   'ServicesController@list_mom_category');
        Route::post('mom/topic',      'ServicesController@list_mom_topic');
        
        //jtc
        Route::post('jtc/centers',   'JtcController@list_centers');
        Route::post('jtc/category',  'JtcController@list_category');
        Route::post('jtc/events',    'JtcController@list_events');
        Route::post('jtc/detail',    'JtcController@detail');
        Route::post('jtc/like',      'JtcController@like_topic');
        Route::post('jtc/comment',   'JtcController@addComment');
        Route::post('jtc/share',     'JtcController@share_topic');
        
        //services
        Route::post('services/list', 'ServicesController@list_services');
        Route::post('services/like', 'ServicesController@like_services');
        Route::post('services/share', 'ServicesController@share_services');
        Route::post('services/view', 'ServicesController@view');
        Route::post('services/comment', 'ServicesController@addComment');

        //bus
        Route::post('bus/service', 'BusController@list_route');
        Route::post('bus/service/stops', 'BusController@list_route_stops');

        Route::post('bus/stops', 'BusController@list_stops');
        Route::post('bus/arrival', 'BusController@getArrival');
        Route::post('bus/share', 'BusController@share_topic');
        Route::post('bus/report', 'BusController@report_topic');

        //forum and topic
        Route::post('topic/list', 'TopicController@list_topics');
        Route::post('topic/like', 'TopicController@like_topic');
        Route::post('topic/favourite', 'TopicController@fav_topic');
        Route::post('topic/share', 'TopicController@share_topic');
        Route::post('topic/report', 'TopicController@report_topic');

        Route::post('category/list', 'ForumController@category_list');
        Route::post('forum/list', 'ForumController@list_forums');
        Route::post('forum/like', 'ForumController@like_forum');
        Route::post('forum/favourite', 'ForumController@fav_forum');
        Route::post('forum/share', 'ForumController@share_forum');
        Route::post('forum/report', 'ForumController@report_forum');
        Route::post('forum/view', 'ForumController@view');
        Route::post('forum/add', 'ForumController@add');
        Route::post('forum/comment', 'ForumController@addComment');

        //course
        Route::post('course/list', 'CourseController@getList');
        Route::post('course/ongoing_list', 'CourseController@getOngoingList');
        Route::post('course/upcoming_list', 'CourseController@getUpcomingList');
        Route::post('course/view', 'CourseController@view');
        Route::post('course/like', 'CourseController@like_course');
        Route::post('course/share', 'CourseController@share_course');
        Route::post('course/join', 'CourseController@join_course');
        Route::post('course/complete', 'CourseController@markComplete');

        //feedback
        Route::post('feedback/add', 'FeedbackController@add');
        //to update fcm token
        Route::post('fcm_token', 'ActivityController@postToken');

        Route::post('incident/add', 'IncidentController@add');
        Route::post('incident/list', 'IncidentController@listout');
        Route::post('incident/view', 'IncidentController@view');

        Route::post('dormitory/add', 'DormitoryController@add');
        Route::post('dormitory/list', 'DormitoryController@listout');
        Route::post('dormitory/view', 'DormitoryController@view');
        Route::post('dormitory/complete', 'DormitoryController@markComplete');

        Route::post('upload/media', 'IncidentController@media_upload');
        Route::post('delete/media', 'IncidentController@media_delete');

        //notification
        Route::post('notification/list', 'NotificationController@listout');
        Route::post('notification/delete', 'NotificationController@destroy');


    });

    Route::group(['prefix' => 'driver', 'middleware' => ['jwt-auth', 'user-blocked']], function () {
        
        Route::post('/invoice/list',        ['uses' => 'DriverController@getInvoiceList']);
        Route::post('/trip/list',           ['uses' => 'DriverController@getTripList']);


        Route::post('/trip/accept',         ['uses' => 'DriverController@acceptTrip']);
        Route::post('/trip/reject',         ['uses' => 'DriverController@rejectTrip']);
        Route::post('/trip/detail',         ['uses' => 'DriverController@getTripDetail']);

        Route::post('/update/status',       ['uses' => 'DriverController@updateStatus']);
        Route::post('/dashboard',           ['uses' => 'DriverController@getDashboard']);
        Route::post('order/list',           ['uses' => 'DriverController@getOrder']);
        Route::post('order/detail',         ['uses' => 'DriverController@getOrderDetail']);

        Route::post('/view',                ['uses' => 'DriverController@viewProfile']);
        // Route::post('/edit',               ['uses' => 'DriverController@getProfile']);
        Route::post('/edit',                ['uses' => 'DriverController@postProfile']);

        Route::post('/earning',             ['uses' => 'DriverController@getEarning']);
        Route::post('/earning/detail',      ['uses' => 'DriverController@getEarningDetail']);
        Route::post('/earning/batch/detail',['uses' => 'DriverController@getTripOrderDetail']);

        Route::post('trip/notification',          ['uses' => 'DriverController@getTrip']);
        Route::post('trip/upcoming',          ['uses' => 'DriverController@getUpcomingTrip']);

        Route::post('order/notification',         ['uses' => 'DriverController@getNewOrder']);
        Route::post('order/accept',               ['uses' => 'DriverController@acceptOrder']);
        Route::post('order/reject',               ['uses' => 'DriverController@rejectOrder']);

        Route::post('order/items',               ['uses' => 'DriverController@getOrderById']);
        Route::post('cron/trip',               ['uses' => 'DriverController@cronTrip']);
    });

    Route::group(['prefix' => 'merchant', 'middleware' => ['jwt-auth', 'user-blocked']], function () {

        Route::post('/item/update',             ['uses' => 'MerchantController@updateItem']);
        Route::post('/order/update',            ['uses' => 'MerchantController@updateOrder']);

        Route::post('/dashboard',               ['uses' => 'MerchantController@getDashboard']);
        Route::post('/orders/new',               ['uses' => 'MerchantController@getNewOrder']);
        Route::post('/orders/upcoming',               ['uses' => 'MerchantController@getUpcomingOrder']);
        Route::post('/orders/all',              ['uses' => 'MerchantController@getTwoUpcomingOrder']);
        Route::post('/order/accept',               ['uses' => 'MerchantController@acceptOrder']);
        Route::post('/order/view',              ['uses' => 'MerchantController@viewOrder']);

        Route::post('/package/subscribed',      ['uses' => 'MerchantController@packageSubscribed']);
        Route::post('/package/subscribers',     ['uses' => 'MerchantController@packageSubscribers']);
        Route::post('/package/subscription',    ['uses' => 'MerchantController@packageSubscription']);

        Route::post('/order/history',           ['uses' => 'MerchantController@viewHistory']);

        Route::post('/view',                    ['uses' => 'MerchantController@viewProfile']);
        Route::post('/edit',                    ['uses' => 'MerchantController@postProfile']);

        Route::post('/menu',                    ['uses' => 'MerchantController@getMenu']);
        Route::post('/menu/view',               ['uses' => 'MerchantController@viewItem']);
        // Route::post('/item/add',                ['uses' => 'MerchantController@getItem']);
        Route::post('item/delete',              ['uses' => 'MerchantController@deleteItem']);
        Route::post('item/add',                 ['uses' => 'MerchantController@postItem']);
        Route::post('item/view',                ['uses' => 'MerchantController@getItemEdit']);
        Route::post('/menu/item/update',              ['uses' => 'MerchantController@postItemEdit']);
        Route::post('/update/item',              ['uses' => 'MerchantController@postItemEdit']);
        Route::post('/image/removeByIndex',      ['uses' => 'MerchantController@deleteImage']);

        // Route::post('/package/add',             ['uses' => 'MerchantController@addPackage']);
        Route::post('package/add',              ['uses' => 'MerchantController@postPackage']);
        Route::post('package/view',             ['uses' => 'MerchantController@editPackage']);
        Route::post('package/update',           ['uses' => 'MerchantController@updatePackage']);

        Route::post('/account',              ['uses' => 'MerchantController@getAccount']);
        Route::post('/account/detail',       ['uses' => 'MerchantController@getAccountDetail']);
        Route::post('/invoice/detail',       ['uses' => 'MerchantController@getInvoiceDetail']);

    });

  });

});
