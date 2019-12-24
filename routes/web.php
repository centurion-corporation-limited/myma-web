<?php

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

// function () {
//   if(Auth::guest())
//     return view('auth.login');
//   else
//     return view('admin.dashboard.index');
//
// }
// Route::get('/upload/excel', 'Admin\DashboardController@uploadExcel');
// Route::get('/upload/sgi', 'Admin\DashboardController@uploadExcelSgi');
// Route::get('/upload/papan', 'Admin\DashboardController@uploadExcelGoodForWalletPapan');


Route::post('/check', ['as' => 'check', 'uses' => 'Admin\DashboardController@check']);
Route::get('/flexm/files', ['as' => 'flexm.files', 'uses' => 'Admin\DashboardController@flexmFiles']);
Route::get('/html/{user_id}', ['as' => 'html',  'uses' => 'Frontend\FrontendController@html']);

Route::get('/delete/users',   'Frontend\FrontendController@deleteUser');

Route::get('/', ['as' => 'home',  'uses' => 'Admin\DashboardController@getIndex']);
Route::get('/batch/add',             ['as' => 'admin.batch.add',    'uses' => 'Admin\DormitoryController@addBatch']);
Route::get('/route/add',             ['as' => 'admin.route.add',    'uses' => 'Admin\DormitoryController@addRoutes']);
Route::get('/stop/add',             ['as' => 'admin.stop.add',    'uses' => 'Admin\DormitoryController@addBusStops']);
Route::get('/verify/fin',             ['as' => 'admin.fin.add',    'uses' => 'Api\ProfileController@verifyFin']);

Route::get('/spuul/carousels',       ['uses' => 'Admin\DormitoryController@getCarousels']);

Route::get('/admin', 'Admin\DashboardController@getIndex');

Route::get('/flexm/upload', ['as' => 'admin.upload.flexm', 'uses' => 'Admin\DashboardController@getUpload']);
Route::post('/flexm/upload', ['as' => 'admin.upload.flexm', 'uses' => 'Admin\DashboardController@postUpload']);

Route::get('/test', 'Admin\DashboardController@getTest');
Route::get('/thank_you', 'Frontend\FrontendController@thankYou');

Route::get('/admin/dashboard', 'Admin\DashboardController@getIndex');

//signup
Route::get('/dms', 'Frontend\FrontendController@getDms')->name('app.search.dms');
Route::get('/sign', 'Frontend\FrontendController@getSignup');
// Route::get('/newsignup', 'Frontend\FrontendController@getSignuppp');
// Route::get('/account', 'Frontend\FrontendController@getSignupp');
Route::get('/account/success', ['as' => 'signup.form.success','uses' => 'Frontend\FrontendController@getSignupSuccess']);
Route::post('registre', ['as' => 'app.register.new', 'uses' => 'Frontend\FrontendController@registerCustNew']);

Route::get('/naanstap/terms-conditions', ['as' => 'naanstap.tnc', 'uses' => 'Frontend\CustomerController@getTnc']);
// Route::get('/webservices', 'HomeController@getServices');
// Route::get('import', 'Admin\OccurenceController@import');
Auth::routes();
Route::get('logout', function(){
  return view('auth.login');
});

// confirm
Route::get('/send', ['as' => 'send', 'uses' => 'Auth\AppRegisterController@sendEmailOtp']);
Route::get('confirm/{key}', ['as' => 'confirm', 'uses' => 'Auth\RegisterController@getConfirm']);
Route::get('confirmed/success', ['as' => 'confirm.success', 'uses' => 'Auth\RegisterController@getSuccess']);

Route::get('/merchant/qrcode/{id}',   ['as' => 'admin.flexm.qrcode', 'uses' => 'Admin\MerchantController@getQRView']);
Route::get('/terminal/qrcode/{id}',   ['as' => 'admin.terminal.qrcode', 'uses' => 'Admin\TerminalController@getQRView']);

//print order invoice
Route::get('/order/invoice/{id}', ['as' => 'food.customer.order.print', 'uses' => 'Frontend\CustomerController@getPrintInvoice']);
// all invoices for customer txns
Route::get('/transaction/invoices/{token}', ['as' => 'customer.invoices.list', 'uses' => 'Frontend\CustomerController@getInvoiceListing']);
Route::get('/transaction/invoice/view/{id}', ['as' => 'customer.invoice.view', 'uses' => 'Frontend\CustomerController@getInvoiceView']);
Route::get('/transaction/invoice/print/{id}', ['as' => 'customer.invoice.print', 'uses' => 'Frontend\CustomerController@getInvoicePrint']);

Route::group(['namespace' => 'Admin', 'prefix' => 'cron'], function () {
    //ad status update
    Route::get('/update/ad',                  ['as' => 'cron.update.ad', 'uses' => 'CronController@updateAdStatus']);
    //flexm merchant add for course user's
    Route::get('/flexm/merchant/add',         ['as' => 'cron.merchant.add', 'uses' => 'CronController@addFlexmMerchant']);
    Route::get('/flexm/merchant/food',        ['as' => 'cron.merchant.food', 'uses' => 'CronController@addFoodMerchant']);

    //upload remittance doc import
    Route::get('/settle',    ['as' => 'cron.flexm.settlement', 'uses' => 'CronController@updateRemittanceVerify']);
    Route::get('/flexm/import/remittance',    ['as' => 'cron.flexm.remittance', 'uses' => 'CronController@updateRemittanceDoc']);
    Route::get('/flexm/import/wallet',        ['as' => 'cron.flexm.wallet', 'uses' => 'CronController@updateWalletDoc']);
    //every day
    Route::get('/payout',                     ['as' => 'cron.payout.check', 'uses' => 'CronController@createPayout']);

    //createtoken
    Route::get('/token',                     ['as' => 'cron.user.token', 'uses' => 'CronController@createToken']);
    //trip - create trip for orders three times a day
    Route::get('/trip/create',                ['as' => 'cron.trip.create', 'uses' => 'CronController@createTrip']);

    //spuul to send notification every day
    Route::get('/spuul/check',                ['as' => 'cron.spuul.check', 'uses' => 'CronController@checkSubscription']);
    //run every day past one hour midnight
    Route::get('/spuul/subscribe',            ['as' => 'cron.spuul.subscribe', 'uses' => 'CronController@subscribeToSpuul']);
});

Route::group(['namespace' => 'Frontend'], function () {
    Route::get('/page/{id}',    ['as' => 'app.page', 'uses' => 'FrontendController@getPage']);

    Route::get('/trans_server',           ['as' => 'trans_server', 'uses' => 'FrontendController@getServer']);
    Route::post('/trans_server',           ['as' => 'trans_server', 'uses' => 'FrontendController@getServer']);
    Route::get('/trans_browser',          ['as' => 'trans_browser', 'uses' => 'FrontendController@getBrowser']);
    Route::post('/trans_browser',          ['as' => 'trans_browser', 'uses' => 'FrontendController@getBrowser']);
    Route::get('/remit',                ['uses' => 'FrontendController@getRemit']);
    Route::get('/enets',                ['uses' => 'FrontendController@postPayment']);
    Route::get('/terms',                ['uses' => 'FrontendController@getTnc']);
    Route::get('/privacy',              ['uses' => 'FrontendController@getPrivacy']);
    Route::get('/faq',                  ['uses' => 'FrontendController@getFAQ']);

    Route::get('/flexm/terms',          ['uses' => 'FrontendController@getFlexmTerms']);
    Route::get('/flexm/guide',          ['uses' => 'FrontendController@getFlexmGuide']);
    Route::get('/flexm/faq',          ['uses' => 'FrontendController@getFlexmFaq']);
    Route::get('/flexm/how',          ['uses' => 'FrontendController@getFlexmHow']);
    Route::get('/flexm/support',          ['uses' => 'FrontendController@getFlexmSupport']);
    // Route::get('/remittance/terms',        ['uses' => 'FrontendController@getRemittanceTerms']);

    Route::get('/contact',              ['as' => 'contact', 'uses' => 'FrontendController@getInfoContact']);
    Route::post('/contact',             ['as' => 'frontend.contact_us', 'uses' => 'FrontendController@postContact']);

    Route::get('/payment',           ['as' => 'frontend.payment', 'uses' => 'FrontendController@getPayment']);

    Route::get('/spuul/payment',     ['as' => 'frontend.spuul.payment', 'uses' => 'FrontendController@getFlexmLogin']);
    Route::get('/spuul/checkout',    ['as' => 'frontend.spuul.checkout', 'uses' => 'FrontendController@getCheckout']);
    Route::post('/spuul/checkout',   ['as' => 'frontend.spuul.checkout', 'uses' => 'FrontendController@postCheckout']);



    Route::group(['prefix' => 'customer', 'middleware' => ['custom-auth']], function () {
        Route::post('/ajax/add/cart',     ['as' => 'ajax.add.cart', 'uses' => 'CustomerController@addCart']);
        Route::post('/ajax/cart/update',  ['as' => 'ajax.update.cart', 'uses' => 'CustomerController@updateCart']);
        Route::post('/ajax/remove/cart',  ['as' => 'ajax.remove.cart', 'uses' => 'CustomerController@removeCart']);

        Route::post('/ajax/order/rate',   ['as' => 'ajax.order.rate', 'uses' => 'CustomerController@addRating']);

        Route::post('/ajax/get/address',  ['as' => 'ajax.get.address', 'uses' => 'CustomerController@getAddress']);
        Route::post('/ajax/order/again',  ['as' => 'ajax.order.again', 'uses' => 'CustomerController@orderAgain']);


        Route::get('/dashboard',          ['as' => 'food.customer.home', 'uses' => 'CustomerController@getDashboard']);

        Route::get('/discount',           ['as' => 'food.customer.discount', 'uses' => 'CustomerController@getDiscount']);

        Route::get('/cuisines',           ['as' => 'food.customer.cuisine', 'uses' => 'CustomerController@getCuisine']);
        Route::get('/order/my',           ['as' => 'food.customer.my_order', 'uses' => 'CustomerController@getMyOrder']);
        Route::get('/order/detail/{id}',  ['as' => 'food.customer.order.detail', 'uses' => 'CustomerController@getOrderDetail'])->where(['id' => '\d+']);

        Route::get('/order/invoice/{id}', ['as' => 'food.customer.order.invoice', 'uses' => 'CustomerController@getInvoice']);

        Route::get('/food/list',          ['as' => 'food.customer.food_list', 'uses' => 'CustomerController@getFoodListing']);
        Route::get('/food/detail/{id}',   ['as' => 'food.customer.food_detail', 'uses' => 'CustomerController@getFoodDetail'])->where(['id' => '\d+']);

        Route::get('/cart',               ['as' => 'food.customer.cart', 'uses' => 'CustomerController@getCart']);
        Route::get('/checkout',           ['as' => 'food.customer.checkout', 'uses' => 'CustomerController@getCheckout']);
        Route::post('/payment',           ['as' => 'food.customer.payment', 'uses' => 'CustomerController@postPayment']);
        Route::get('/payment/success',    ['as' => 'food.customer.payment.success', 'uses' => 'CustomerController@getPaymentSuccess']);

        Route::get('/flexm/pay',          ['as' => 'food.customer.pay', 'uses' => 'CustomerController@getPaymentPage']);
        Route::post('/flexm/pay',         ['as' => 'food.customer.pay', 'uses' => 'CustomerController@postPaymentPage']);

        Route::get('/flexm/login',        ['as' => 'flexm.login.page', 'uses' => 'CustomerController@getFlexmLogin']);
        Route::post('/flexm/login',       ['as' => 'flexm.login', 'uses' => 'CustomerController@loginFlexm']);

        Route::get('/subscription/{id}',  ['as' => 'food.customer.subscription', 'uses' => 'CustomerController@getSubscription'])->where(['id' => '\d+']);
        Route::get('/package',            ['as' => 'food.customer.package', 'uses' => 'CustomerController@getPackage']);

    });

    Route::group(['prefix' => 'driver'], function () {

        Route::get('/login',              ['as' => 'merchant.login', 'uses' => 'MerchantController@getLogin']);
        // Route::post('/login',          ['as' => 'merchant.login', 'uses' => 'MerchantController@postLogin']);

        Route::post('/ajax/trip/accept',        ['as' => 'ajax.trip.accept', 'uses' => 'DriverController@acceptTrip']);
        Route::post('/ajax/trip/reject',        ['as' => 'ajax.trip.reject', 'uses' => 'DriverController@rejectTrip']);

        Route::group(['middleware' => ['auth', 'acl']], function () {
            Route::post('/update/status',     ['as' => 'driver.update.status',   'uses' => 'DriverController@updateStatus']);
            Route::get('/dashboard',          ['as' => 'driver.home',           'uses' => 'DriverController@getDashboard']);
            Route::get('/order/list/{id}',    ['as' => 'driver.order.list',     'uses' => 'DriverController@getOrder']);
            Route::get('/order/detail/{id}',  ['as' => 'driver.order.detail',   'uses' => 'DriverController@getOrderDetail'])->where(['id' => '\d+']);

            Route::get('/view',               ['as' => 'driver.profile.view',   'uses' => 'DriverController@viewProfile']);
            Route::get('/edit',               ['as' => 'driver.profile.edit',   'uses' => 'DriverController@getProfile']);
            Route::post('/edit',              ['as' => 'driver.profile.edit',   'uses' => 'DriverController@postProfile']);

            Route::get('/earning',            ['as' => 'driver.earning.list',   'uses' => 'DriverController@getEarning']);
            Route::get('/earning/detail/{id}',['as' => 'driver.earning.detail', 'uses' => 'DriverController@getEarningDetail'])->where(['id' => '\d+']);
            Route::get('/earning/batch/detail/{id}',['as' => 'driver.batch.detail', 'uses' => 'DriverController@getTripOrderDetail'])->where(['id' => '\d+']);

            Route::get('/trip',               ['as' => 'driver.trip.notification',   'uses' => 'DriverController@getTrip']);
        });
    });

    Route::group(['prefix' => 'merchant'], function () {
        Route::get('/login',           ['as' => 'merchant.login', 'uses' => 'MerchantController@getLogin']);
        Route::post('/login',           ['as' => 'merchant.login', 'uses' => 'MerchantController@postLogin']);

        Route::post('/ajax/item/update',           ['as' => 'ajax.item.update', 'uses' => 'MerchantController@updateItem']);
        Route::post('/ajax/order/update',           ['as' => 'ajax.order.update', 'uses' => 'MerchantController@updateOrder']);

        Route::group(['middleware' => ['auth', 'acl']], function () {

            Route::get('/dashboard',           ['as' => 'merchant.home', 'uses' => 'MerchantController@getDashboard']);
            Route::get('/order/view/{id}',          ['as' => 'merchant.order.view', 'uses' => 'MerchantController@viewOrder'])->where(['id' => '\d+']);

            Route::get('/package/subscribed',        ['as' => 'merchant.package.subscribed', 'uses' => 'MerchantController@packageSubscribed']);
            Route::get('/package/subscribers/{id}',       ['as' => 'merchant.package.subscribers', 'uses' => 'MerchantController@packageSubscribers'])->where(['id' => '\d+']);
            Route::get('/package/subscription/{id}/{item_id}',      ['as' => 'merchant.package.subscription', 'uses' => 'MerchantController@packageSubscription'])->where(['id' => '\d+','item_id' => '\d+']);;

            Route::get('/order/history',      ['as' => 'merchant.order.history', 'uses' => 'MerchantController@viewHistory']);

            Route::get('/view',    ['as' => 'merchant.profile.view',    'uses' => 'MerchantController@viewProfile']);
            Route::get('/edit',    ['as' => 'merchant.profile.edit',    'uses' => 'MerchantController@getProfile']);
            Route::post('/edit',   ['as' => 'merchant.profile.edit',    'uses' => 'MerchantController@postProfile']);

            Route::get('/menu',             ['as' => 'merchant.menu.list',      'uses' => 'MerchantController@getMenu']);
            Route::get('/menu/view/{id}',   ['as' => 'merchant.menu.view',      'uses' => 'MerchantController@viewItem'])->where(['id' => '\d+']);
            Route::get('/item/add',         ['as' => 'merchant.item.add',       'uses' => 'MerchantController@getItem']);
            Route::post('item/add',         ['as' => 'merchant.item.add',       'uses' => 'MerchantController@postItem']);
            Route::get('/item/edit/{id}',   ['as' => 'merchant.item.edit',      'uses' => 'MerchantController@getItemEdit'])->where(['id' => '\d+']);
            Route::post('item/edit/{id}',   ['as' => 'merchant.item.edit',      'uses' => 'MerchantController@postItemEdit'])->where(['id' => '\d+']);

            Route::get('/package/add',         ['as' => 'merchant.package.add',       'uses' => 'MerchantController@addPackage']);
            Route::post('package/add',         ['as' => 'merchant.package.add',       'uses' => 'MerchantController@postPackage']);
            Route::get('/package/edit/{id}',   ['as' => 'merchant.package.edit',      'uses' => 'MerchantController@editPackage'])->where(['id' => '\d+']);
            Route::post('package/edit/{id}',   ['as' => 'merchant.package.edit',      'uses' => 'MerchantController@updatePackage'])->where(['id' => '\d+']);

            Route::get('/account',              ['as' => 'merchant.account.list',   'uses' => 'MerchantController@getAccount']);
            Route::get('/account/detail/{id}',       ['as' => 'merchant.account.detail', 'uses' => 'MerchantController@getAccountDetail']);
            Route::get('/invoice/detail/{id}',       ['as' => 'merchant.invoice.detail', 'uses' => 'MerchantController@getInvoiceDetail']);

            // Route::get('/food/detail/{id}',           ['as' => 'food.customer.food_detail', 'uses' => 'CustomerController@getFoodDetail'])->where(['id' => '\d+']);

            // Route::get('/cart',           ['as' => 'food.customer.cart', 'uses' => 'CustomerController@getCart']);
            // Route::get('/checkout',           ['as' => 'food.customer.checkout', 'uses' => 'CustomerController@getCheckout']);
            // Route::get('/payment',           ['as' => 'food.customer.payment', 'uses' => 'CustomerController@postPayment']);
            // Route::get('/payment/success',           ['as' => 'food.customer.payment.success', 'uses' => 'CustomerController@getPaymentSuccess']);
            //
            // Route::get('/subscription',           ['as' => 'food.customer.subscription', 'uses' => 'CustomerController@getSubscription']);

        });

    });

});


Route::group(['prefix' => 'mwc', 'namespace' => 'Frontend'], function () {
    Route::get('/are',           ['uses' => 'FrontendController@getAre']);
    Route::get('/clinic',           ['uses' => 'FrontendController@getClinic']);
    Route::get('/contact',           ['uses' => 'FrontendController@getContact']);
    Route::get('/do',           ['uses' => 'FrontendController@getDo']);
    Route::get('/fair',           ['uses' => 'FrontendController@getFair']);
    Route::get('/fair/signup',           ['uses' => 'FrontendController@getFirSignup']);
    Route::get('/help',           ['uses' => 'FrontendController@getHelp']);
    Route::get('/kiosk',           ['uses' => 'FrontendController@getKiosk']);
});


//admin routes
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
// 'is' =>'admin|sub_admin'
    Route::post('/ajax/add/token',            ['as' => 'ajax.add.token', 'uses' => 'AjaxController@addToken']);

    Route::post('/ajax/remove/image',         ['as' => 'ajax.food.image.remove', 'uses' => 'FoodController@removeImage']);
    Route::post('/ajax/calcPrice',            ['as' => 'ajax.food.calcprice', 'uses' => 'FoodController@calcPrice']);
    Route::post('/ajax/food/listing',         ['as' => 'ajax.food.listing', 'uses' => 'FoodController@foodListing']);

    Route::post('/ajax/remove/file',         ['as' => 'ajax.remove.file', 'uses' => 'CourseController@removeFile']);
    Route::get('/ajax/tags/add',     ['as' => 'ajax.add.tag', 'uses' => 'AjaxController@addTag']);

    Route::get('/ajax/language/convert',     ['as' => 'ajax.language.convert', 'uses' => 'AjaxController@convert']);
    Route::post('/ajax/apply/coupon',         ['as' => 'ajax.apply.coupon', 'uses' => 'AjaxController@applyCoupon']);
    Route::post('/ajax/remove/coupon',         ['as' => 'ajax.remove.coupon', 'uses' => 'AjaxController@removeCoupon']);

    Route::post('/ajax/payout/verify',    ['as' => 'ajax.payout.verify',     'uses' => 'PayoutController@verify']);

    Route::group(['middleware' => ['auth', 'acl']], function () {
        Route::get('/',           ['as' => 'admin.dashboard', 'uses' => 'DashboardController@getIndex']);

        Route::get('/logged/in',           ['as' => 'admin.logged.in', 'uses' => 'DashboardController@getLoggedIn']);
        Route::get('/logged/out',          ['as' => 'admin.logged.out', 'uses' => 'DashboardController@getLoggedOut']);

        Route::get('/ajax/logs',           ['as' => 'admin.get.logs', 'uses' => 'DashboardController@getAjaxLogs']);
        Route::get('/logs',                ['as' => 'admin.activity.logs', 'uses' => 'DashboardController@getLogs']);
        Route::get('/logs/delete/{id}',    ['as' => 'admin.logs.delete', 'uses' => 'DashboardController@getDelete']);

        // Route::get('/share',               ['as' => 'admin.share.list', 'uses' => 'DashboardController@getShare']);
        // Route::get('/share/edit/{id}',     ['as' => 'admin.share.edit', 'uses' => 'DashboardController@editShare']);
        // Route::post('/share/edit/{id}',    ['as' => 'admin.share.edit', 'uses' => 'DashboardController@postShare']);


        Route::get('/redeem',                  ['as' => 'admin.redeem.report','uses' => 'RedeemController@getRedeemReport']);
        Route::get('/redeem/download',         ['as' => 'admin.redeem.download','uses' => 'RedeemController@exportRedeemReport']);
        Route::get('/revenue',                  ['as' => 'admin.revenue.report','uses' => 'RevenueController@getReport']);
        Route::get('/revenue/download',         ['as' => 'admin.revenue.download','uses' => 'RevenueController@exportRevenueReport']);
        // Route::post('/revenue/download',         ['as' => 'admin.revenue.download','uses' => 'RevenueController@postDownload']);
        Route::get('/payment',                  ['as' => 'admin.payment.report','uses' => 'PayoutController@getPaymentReport']);
        Route::get('/payment/paid',             ['as' => 'admin.payment.paid','uses' => 'PayoutController@getPaidPayment']);
        Route::get('/payment/export',           ['as' => 'admin.payment.export','uses' => 'PayoutController@exportPaymentReport']);
        Route::get('/payment/download',         ['as' => 'admin.payment.download','uses' => 'PayoutController@getDownload']);
        Route::post('/payment/download',         ['as' => 'admin.payment.download','uses' => 'PayoutController@postDownload']);

        Route::get('/revenue_debug',            ['uses' => 'DebugController@getReport']);

        Route::get('/singx/list',               ['as' => 'admin.singx.list',  'uses' => 'SingxController@getList']);
        Route::get('/singx/remittance',         ['as' => 'admin.singx.remittance',  'uses' => 'SingxController@getRemittance']);
        Route::get('/singx/export',             ['as' => 'admin.singx.export',  'uses' => 'SingxController@exportExcel']);
        Route::get('/singx/remittance/view/{id}',         ['as' => 'admin.singx.remittance.view',  'uses' => 'SingxController@getRemittanceView']);

        Route::get('/share/catering',            ['as' => 'admin.share.catering',  'uses' => 'ShareController@getCateringShare']);
        Route::get('/share/naanstap',            ['as' => 'admin.share.naanstap',  'uses' => 'ShareController@getNaanstapShare']);
        Route::get('/share/singx',               ['as' => 'admin.share.singx',  'uses' => 'ShareController@getSingxShare']);
        Route::get('/share/spuul',               ['as' => 'admin.share.spuul',  'uses' => 'ShareController@getSpuulShare']);
        Route::get('/share/flexm',               ['as' => 'admin.share.flexm',  'uses' => 'ShareController@getFlexmShare']);
        Route::post('/share/flexm',              ['as' => 'admin.share.flexm',  'uses' => 'ShareController@postFlexmShare']);
        Route::post('/share/update/{id}',        ['as' => 'admin.share.update', 'uses' => 'ShareController@postShare']);

        Route::get('/share/course',              ['as' => 'admin.share.courses','uses' => 'ShareController@getCourseShare']);
        Route::get('/share/course/edit/{id}',    ['as' => 'admin.share.edit',   'uses' => 'ShareController@getCourseShareEdit']);
        Route::post('/share/course/edit/{id}',   ['as' => 'admin.share.edit',   'uses' => 'ShareController@updateCourseShare']);

        Route::get('/share/food',                ['as' => 'admin.share.food','uses' => 'ShareController@getFoodShare']);
        Route::get('/share/food/edit/{id}',      ['as' => 'admin.share.food.edit',   'uses' => 'ShareController@getFoodShareEdit']);
        Route::post('/share/food/edit/{id}',     ['as' => 'admin.share.food.edit',   'uses' => 'ShareController@updateFoodShare']);


        Route::get('/transactions/food',          ['as' => 'admin.food.transactions', 'uses' => 'TransactionController@getFoodTransactions']);
        Route::get('/transactions/food/view/{id}',          ['as' => 'admin.food.transaction.view', 'uses' => 'TransactionController@viewFoodTransactions']);

        Route::get('/transactions/inapp',         ['as' => 'admin.transactions.inapp', 'uses' => 'TransactionController@getInappTransactions']);
        Route::get('/transactions/instore',       ['as' => 'admin.transactions.instore', 'uses' => 'TransactionController@getInstoreTransactions']);
        Route::get('/transactions/remittance',    ['as' => 'admin.transactions.remit', 'uses' => 'TransactionController@getRemitTransactions']);
        Route::get('/transactions/wallet',        ['as' => 'admin.transactions.wallet', 'uses' => 'TransactionController@getWalletTransactions']);

        Route::get('/transaction/view/{id}',   ['as' => 'admin.transaction.view', 'uses' => 'DashboardController@viewTransaction']);
        Route::get('/transaction/edit/{id}',   ['as' => 'admin.transaction.edit', 'uses' => 'DashboardController@editTransaction']);
        Route::post('/transaction/edit/{id}',  ['as' => 'admin.transaction.edit', 'uses' => 'DashboardController@postTransaction']);

        Route::get('/transactions/export',     ['as' => 'admin.transactions.export',    'uses' => 'TransactionController@exportExcel']);
        Route::get('/transactions/print/{id}',      ['as' => 'admin.transactions.print', 'uses' => 'TransactionController@getInvoice']);
        
        Route::get('/remittance/view/{id}',   ['as' => 'admin.remit.view', 'uses' => 'TransactionController@viewTransaction']);

        //payout
        Route::get('/payout/users',     ['as' => 'admin.payout.users', 'uses' => 'PayoutController@getUsers']);
        Route::get('/payout/wlc',     ['as' => 'admin.payout.wlc', 'uses' => 'PayoutController@getWlcPayout']);
        Route::get('/payout/transactions/list',      ['as' => 'admin.payout.transactions', 'uses' => 'PayoutController@getList']);
        Route::get('/payout/transactions/food',      ['as' => 'admin.payout.transactions.food', 'uses' => 'PayoutController@getFoodList']);
        Route::get('/payout/view',      ['as' => 'admin.payout.view', 'uses' => 'PayoutController@getView']);
        Route::get('/payout/food',      ['as' => 'admin.payout.food.view', 'uses' => 'PayoutController@getFoodView']);
        Route::get('/payout/list',      ['as' => 'admin.payout.list', 'uses' => 'PayoutController@getPayoutList']);

        Route::post('/payout/save',      ['as' => 'admin.payout.save', 'uses' => 'PayoutController@savePayout']);

        //ajax
        Route::group(['prefix' => 'ajax'], function () {
          Route::get('/get',          ['as' => 'admin.user.get', 'uses' => 'AjaxController@getUser']);
        });

        Route::group(['prefix' => 'spuul'], function () {

            Route::group(['prefix' => 'plan'], function () {
                Route::get('/',             ['as' => 'admin.spuul.plan.list',    'uses' => 'SpuulController@getList', 'can' => 'view.spuul-list']);
                Route::get('/add',          ['as' => 'admin.spuul.plan.add',     'uses' => 'SpuulController@getAdd', 'can' => 'create.spuul-add']);
                Route::post('/add',         ['as' => 'admin.spuul.plan.add',     'uses' => 'SpuulController@postAdd', 'can' => 'create.spuul-add']);
                Route::get('/edit/{id}',    ['as' => 'admin.spuul.plan.edit',    'uses' => 'SpuulController@getEdit', 'can' => 'update.spuul-edit']);
                Route::post('/edit/{id}',   ['as' => 'admin.spuul.plan.edit',    'uses' => 'SpuulController@postEdit', 'can' => 'update.spuul-edit']);
                Route::get('/delete/{id}',  ['as' => 'admin.spuul.plan.delete',  'uses' => 'SpuulController@getDelete', 'can' => 'delete.spuul-edit']);
            });

            Route::get('/transactions',       ['as' => 'spuul.transactions',     'uses' => 'SpuulController@getTransactions']);
            Route::get('/transaction/{id}',   ['as' => 'spuul.transactions.view',   'uses' => 'SpuulController@viewTransaction']);

        });

        Route::group(['prefix' => 'vendors'], function () {
            Route::get('/',             ['as' => 'admin.merchant.list',    'uses' => 'MerchantController@getList',  'can' => 'view.merchant-list']);
            Route::get('/add',          ['as' => 'admin.merchant.add',     'uses' => 'MerchantController@getAdd',   'can' => 'create.merchant-add']);
            Route::post('/add',         ['as' => 'admin.merchant.add',     'uses' => 'MerchantController@postAdd',  'can' => 'create.merchant-add']);
            Route::get('/edit/{id}',    ['as' => 'admin.merchant.edit',    'uses' => 'MerchantController@getEdit',  'can' => 'update.merchant-edit']);
            Route::post('/edit/{id}',   ['as' => 'admin.merchant.edit',    'uses' => 'MerchantController@postEdit', 'can' => 'update.merchant-edit']);
            Route::get('/delete/{id}',  ['as' => 'admin.merchant.delete',  'uses' => 'MerchantController@getDelete','can' => 'delete.merchant-delete']);
        });

        Route::group(['prefix' => 'terminal'], function () {
            Route::get('/',             ['as' => 'admin.terminal.list',    'uses' => 'TerminalController@getList',  'can' => 'view.merchant-list']);
            Route::get('/add',          ['as' => 'admin.terminal.add',     'uses' => 'TerminalController@getAdd',   'can' => 'create.merchant-add']);
            Route::post('/add',         ['as' => 'admin.terminal.add',     'uses' => 'TerminalController@postAdd',  'can' => 'create.merchant-add']);
            Route::get('/edit/{id}',    ['as' => 'admin.terminal.edit',    'uses' => 'TerminalController@getEdit',  'can' => 'update.merchant-edit']);
            Route::post('/edit/{id}',   ['as' => 'admin.terminal.edit',    'uses' => 'TerminalController@postEdit', 'can' => 'update.merchant-edit']);
            Route::get('/delete/{id}',  ['as' => 'admin.terminal.delete',  'uses' => 'TerminalController@getDelete','can' => 'delete.merchant-edit']);
        });

        // order
        Route::group(['prefix' => 'order'], function () {
          Route::get('/',             ['as' => 'admin.order.list',    'uses' => 'OrderController@getList']);
          // Route::get('/add',          ['as' => 'admin.order.add',     'uses' => 'OrderController@getAdd']);
          // Route::post('/add',         ['as' => 'admin.order.add',     'uses' => 'OrderController@postAdd']);
          Route::get('/view/{id}',    ['as' => 'admin.order.view',    'uses' => 'OrderController@getView']);
          Route::get('/edit/{id}',    ['as' => 'admin.order.edit',    'uses' => 'OrderController@getEdit']);
          Route::post('/edit/{id}',   ['as' => 'admin.order.edit',    'uses' => 'OrderController@postEdit']);
          Route::get('/delete/{id}',  ['as' => 'admin.order.delete',  'uses' => 'OrderController@getDelete']);

          Route::get('/invoices',     ['as' => 'admin.order.invoices',    'uses' => 'OrderController@getInvoices']);
          Route::post('/invoices',     ['as' => 'admin.order.invoices',    'uses' => 'OrderController@postInvoices']);

          Route::get('/invoice/wlc/{id}',     ['as' => 'admin.order.invoice.wlc',    'uses' => 'OrderController@getWlcInvoice']);
          Route::get('/invoice/merchant/{id}',     ['as' => 'admin.order.invoice.merchant',    'uses' => 'OrderController@getMerchantInvoice']);

          Route::get('/batch',      ['as' => 'admin.batch.search',    'uses' => 'OrderController@getBatch']);
          Route::post('/batch',     ['as' => 'admin.batch.search',    'uses' => 'OrderController@postBatch']);
          
          Route::get('/worker',      ['as' => 'admin.batch.worker',    'uses' => 'OrderController@getWorker']);
          Route::get('/worker/export/{batch_id}',     ['as' => 'admin.batch.worker.export',    'uses' => 'OrderController@exportWorker']);
        });

        // subscriptions
        Route::group(['prefix' => 'subscriptions'], function () {
          Route::get('/',             ['as' => 'admin.subscriptions.list',    'uses' => 'SubscriptionsController@getList']);
          // Route::get('/add',          ['as' => 'admin.order.add',     'uses' => 'OrderController@getAdd']);
          // Route::post('/add',         ['as' => 'admin.order.add',     'uses' => 'OrderController@postAdd']);
          Route::get('/view/{id}',    ['as' => 'admin.subscriptions.view',    'uses' => 'SubscriptionsController@getView']);
          Route::get('/edit/{id}',    ['as' => 'admin.subscriptions.edit',    'uses' => 'SubscriptionsController@getEdit']);
          Route::post('/edit/{id}',   ['as' => 'admin.subscriptions.edit',    'uses' => 'SubscriptionsController@postEdit']);
          Route::get('/delete/{id}',  ['as' => 'admin.subscriptions.delete',  'uses' => 'SubscriptionsController@getDelete']);

          Route::get('/view/detail/{id}',    ['as' => 'admin.subscription.detail',    'uses' => 'SubscriptionsController@getSubscriptionView']);
        });

        // restaurant
        Route::group(['prefix' => 'restaurant', 'is' =>'food-admin'], function () {
          Route::get('/',             ['as' => 'admin.restaurant.list',    'uses' => 'RestaurantController@getList']);
          Route::get('/add',          ['as' => 'admin.restaurant.add',     'uses' => 'RestaurantController@getAdd']);
          Route::post('/add',         ['as' => 'admin.restaurant.add',     'uses' => 'RestaurantController@postAdd']);
          Route::get('/edit/{id}',    ['as' => 'admin.restaurant.edit',    'uses' => 'RestaurantController@getEdit']);
          Route::post('/edit/{id}',   ['as' => 'admin.restaurant.edit',    'uses' => 'RestaurantController@postEdit']);
          Route::get('/delete/{id}',  ['as' => 'admin.restaurant.delete',  'uses' => 'RestaurantController@getDelete']);
        });

        Route::group(['prefix' => 'mom'], function () {
            Route::group(['prefix' => 'category'], function () {
              Route::get('/',             ['as' => 'admin.mom.category.list',    'uses' => 'MomController@getCategoryList','can' => 'view.mom-category-list']);
              Route::get('/add',          ['as' => 'admin.mom.category.add',     'uses' => 'MomController@getCategoryAdd','can' => 'create.mom-category-add']);
              Route::post('/add',         ['as' => 'admin.mom.category.add',     'uses' => 'MomController@postCategoryAdd','can' => 'create.mom-category-add']);
              Route::get('/view/{id}',    ['as' => 'admin.mom.category.view',    'uses' => 'MomController@getCategoryView','can' => 'view.mom-category-list']);
              Route::get('/edit/{id}',    ['as' => 'admin.mom.category.edit',    'uses' => 'MomController@getCategoryEdit','can' => 'update.mom-category-edit']);
              Route::post('/edit/{id}',   ['as' => 'admin.mom.category.edit',    'uses' => 'MomController@postCategoryEdit','can' => 'update.mom-category-edit']);
              Route::get('/delete/{id}',  ['as' => 'admin.mom.category.delete',  'uses' => 'MomController@getCategoryDelete','can' => 'delete.mom-category-delete']);
            });
            Route::group(['prefix' => 'topic'], function () {
              Route::get('/',             ['as' => 'admin.mom.topic.list',    'uses' => 'MomController@getTopicList','can' => 'view.mom-topic-list']);
              Route::get('/add',          ['as' => 'admin.mom.topic.add',     'uses' => 'MomController@getTopicAdd','can' => 'create.mom-topic-add']);
              Route::post('/add',         ['as' => 'admin.mom.topic.add',     'uses' => 'MomController@postTopicAdd','can' => 'create.mom-topic-add']);
              Route::get('/view/{id}',    ['as' => 'admin.mom.topic.view',    'uses' => 'MomController@getTopicView','can' => 'view.mom-topic-list']);
              Route::get('/edit/{id}',    ['as' => 'admin.mom.topic.edit',    'uses' => 'MomController@getTopicEdit','can' => 'update.mom-topic-edit']);
              Route::post('/edit/{id}',   ['as' => 'admin.mom.topic.edit',    'uses' => 'MomController@postTopicEdit','can' => 'update.mom-topic-edit']);
              Route::get('/delete/{id}',  ['as' => 'admin.mom.topic.delete',  'uses' => 'MomController@getTopicDelete','can' => 'delete.mom-topic-delete']);
            });
        });
        
        //jtc
        Route::group(['prefix' => 'jtc' , 'namespace' => 'JTC'], function () {
            Route::group(['prefix' => 'centers'], function () {
              Route::get('/',             ['as' => 'admin.jtc.centers.list',    'uses' => 'CenterController@getCategoryList','can' => 'view.jtc-list']);
              Route::get('/add',          ['as' => 'admin.jtc.centers.add',     'uses' => 'CenterController@getCategoryAdd','can' => 'view.jtc-list']);
              Route::post('/add',         ['as' => 'admin.jtc.centers.add',     'uses' => 'CenterController@postCategoryAdd','can' => 'view.jtc-list']);
              Route::get('/view/{id}',    ['as' => 'admin.jtc.centers.view',    'uses' => 'CenterController@getCategoryView','can' => 'view.jtc-list']);
              Route::get('/edit/{id}',    ['as' => 'admin.jtc.centers.edit',    'uses' => 'CenterController@getCategoryEdit','can' => 'view.jtc-list']);
              Route::post('/edit/{id}',   ['as' => 'admin.jtc.centers.edit',    'uses' => 'CenterController@postCategoryEdit','can' => 'view.jtc-list']);
              Route::get('/delete/{id}',  ['as' => 'admin.jtc.centers.delete',  'uses' => 'CenterController@getCategoryDelete','can' => 'view.jtc-list']);
            });
            Route::group(['prefix' => 'category'], function () {
              Route::get('/',             ['as' => 'admin.jtc.category.list',    'uses' => 'CategoryController@getCategoryList','can' => 'view.jtc-list']);
              Route::get('/add',          ['as' => 'admin.jtc.category.add',     'uses' => 'CategoryController@getCategoryAdd','can' => 'view.jtc-list']);
              Route::post('/add',         ['as' => 'admin.jtc.category.add',     'uses' => 'CategoryController@postCategoryAdd','can' => 'view.jtc-list']);
              Route::get('/view/{id}',    ['as' => 'admin.jtc.category.view',    'uses' => 'CategoryController@getCategoryView','can' => 'view.jtc-list']);
              Route::get('/edit/{id}',    ['as' => 'admin.jtc.category.edit',    'uses' => 'CategoryController@getCategoryEdit','can' => 'view.jtc-list']);
              Route::post('/edit/{id}',   ['as' => 'admin.jtc.category.edit',    'uses' => 'CategoryController@postCategoryEdit','can' => 'view.jtc-list']);
              Route::get('/delete/{id}',  ['as' => 'admin.jtc.category.delete',  'uses' => 'CategoryController@getCategoryDelete','can' => 'view.jtc-list']);
            });
            Route::group(['prefix' => 'event'], function () {
              Route::get('/',             ['as' => 'admin.jtc.event.list',    'uses' => 'EventController@getCategoryList','can' => 'view.jtc-list']);
              Route::get('/add',          ['as' => 'admin.jtc.event.add',     'uses' => 'EventController@getCategoryAdd','can' => 'view.jtc-list']);
              Route::post('/add',         ['as' => 'admin.jtc.event.add',     'uses' => 'EventController@postCategoryAdd','can' => 'view.jtc-list']);
              Route::get('/view/{id}',    ['as' => 'admin.jtc.event.view',    'uses' => 'EventController@getCategoryView','can' => 'view.jtc-list']);
              Route::get('/edit/{id}',    ['as' => 'admin.jtc.event.edit',    'uses' => 'EventController@getCategoryEdit','can' => 'view.jtc-list']);
              Route::post('/edit/{id}',   ['as' => 'admin.jtc.event.edit',    'uses' => 'EventController@postCategoryEdit','can' => 'view.jtc-list']);
              Route::get('/delete/{id}',  ['as' => 'admin.jtc.event.delete',  'uses' => 'EventController@getCategoryDelete','can' => 'view.jtc-list']);
            });
            Route::group(['prefix' => 'detail'], function () {
              Route::get('/',             ['as' => 'admin.jtc.detail.list',    'uses' => 'DetailController@getTopicList','can' => 'view.jtc-list']);
              Route::get('/add',          ['as' => 'admin.jtc.detail.add',     'uses' => 'DetailController@getTopicAdd','can' => 'view.jtc-list']);
              Route::post('/add',         ['as' => 'admin.jtc.detail.add',     'uses' => 'DetailController@postTopicAdd','can' => 'view.jtc-list']);
              Route::get('/view/{id}',    ['as' => 'admin.jtc.detail.view',    'uses' => 'DetailController@getTopicView','can' => 'view.jtc-list']);
              Route::get('/edit/{id}',    ['as' => 'admin.jtc.detail.edit',    'uses' => 'DetailController@getTopicEdit','can' => 'view.jtc-list']);
              Route::post('/edit/{id}',   ['as' => 'admin.jtc.detail.edit',    'uses' => 'DetailController@postTopicEdit','can' => 'view.jtc-list']);
              Route::get('/delete/{id}',  ['as' => 'admin.jtc.detail.delete',  'uses' => 'DetailController@getTopicDelete','can' => 'view.jtc-list']);
            });

            Route::group(['prefix' => 'comments'], function () {
              Route::get('/',             ['as' => 'admin.jtc.comments.list',    'uses' => 'CommentsController@getTopicList','can' => 'view.jtc-list']);
              Route::get('/edit/{id}',    ['as' => 'admin.jtc.comments.edit',    'uses' => 'CommentsController@getTopicEdit','can' => 'view.jtc-list']);
              Route::post('/edit/{id}',   ['as' => 'admin.jtc.comments.edit',    'uses' => 'CommentsController@postTopicEdit','can' => 'view.jtc-list']);
              Route::get('/delete/{id}',  ['as' => 'admin.jtc.comments.delete',  'uses' => 'CommentsController@getTopicDelete','can' => 'view.jtc-list']);
            });
        });

        // food
        Route::group(['prefix' => 'food'], function () {

            Route::group(['prefix' => 'coupon'], function () {
              Route::get('/',             ['as' => 'admin.coupon.list',    'uses' => 'CouponController@getList']);
              Route::get('/add',          ['as' => 'admin.coupon.add',     'uses' => 'CouponController@getAdd']);
              Route::post('/add',         ['as' => 'admin.coupon.add',     'uses' => 'CouponController@postAdd']);
              Route::get('/edit/{id}',    ['as' => 'admin.coupon.edit',    'uses' => 'CouponController@getEdit']);
              Route::post('/edit/{id}',   ['as' => 'admin.coupon.edit',    'uses' => 'CouponController@postEdit']);
              Route::get('/delete/{id}',  ['as' => 'admin.coupon.delete',  'uses' => 'CouponController@getDelete']);
            });

            Route::group(['prefix' => 'trip'], function () {
              Route::get('/',             ['as' => 'admin.trip.list',    'uses' => 'TripController@getList']);
              Route::get('/add',          ['as' => 'admin.trip.add',     'uses' => 'TripController@getAdd']);
              Route::post('/add',         ['as' => 'admin.trip.add',     'uses' => 'TripController@postAdd']);
              Route::get('/edit/{id}',    ['as' => 'admin.trip.edit',    'uses' => 'TripController@getEdit']);
              Route::post('/edit/{id}',   ['as' => 'admin.trip.edit',    'uses' => 'TripController@postEdit']);
              Route::get('/delete/{id}',  ['as' => 'admin.trip.delete',  'uses' => 'TripController@getDelete']);
            });

            // Route::group(['prefix' => 'discount'], function () {
            //   Route::get('/',             ['as' => 'admin.discount.list',    'uses' => 'DiscountController@getList']);
            //   Route::get('/add',          ['as' => 'admin.discount.add',     'uses' => 'DiscountController@getAdd']);
            //   Route::post('/add',         ['as' => 'admin.discount.add',     'uses' => 'DiscountController@postAdd']);
            //   Route::get('/edit/{id}',    ['as' => 'admin.discount.edit',    'uses' => 'DiscountController@getEdit'])->where(['id' => '\d+']);
            //   Route::post('/edit/{id}',   ['as' => 'admin.discount.edit',    'uses' => 'DiscountController@postEdit'])->where(['id' => '\d+']);
            //   Route::get('/delete/{id}',  ['as' => 'admin.discount.delete',  'uses' => 'DiscountController@getDelete'])->where(['id' => '\d+']);
            // });

            Route::group(['prefix' => 'category'], function () {
              Route::get('/',             ['as' => 'admin.food_category.list',    'uses' => 'RestaurantController@getCategoryList']);
              Route::get('/add',          ['as' => 'admin.food_category.add',     'uses' => 'RestaurantController@getCategoryAdd']);
              Route::post('/add',         ['as' => 'admin.food_category.add',     'uses' => 'RestaurantController@postCategoryAdd']);
              Route::get('/edit/{id}',    ['as' => 'admin.food_category.edit',    'uses' => 'RestaurantController@getCategoryEdit']);
              Route::post('/edit/{id}',   ['as' => 'admin.food_category.edit',    'uses' => 'RestaurantController@postCategoryEdit']);
              Route::get('/delete/{id}',  ['as' => 'admin.food_category.delete',  'uses' => 'RestaurantController@getCategoryDelete']);
            });

            Route::group(['prefix' => 'course'], function () {
              Route::get('/',             ['as' => 'admin.food_course.list',    'uses' => 'RestaurantController@getCourseList']);
              Route::get('/add',          ['as' => 'admin.food_course.add',     'uses' => 'RestaurantController@getCourseAdd']);
              Route::post('/add',         ['as' => 'admin.food_course.add',     'uses' => 'RestaurantController@postCourseAdd']);
              Route::get('/edit/{id}',    ['as' => 'admin.food_course.edit',    'uses' => 'RestaurantController@getCourseEdit']);
              Route::post('/edit/{id}',   ['as' => 'admin.food_course.edit',    'uses' => 'RestaurantController@postCourseEdit']);
              Route::get('/delete/{id}',  ['as' => 'admin.food_course.delete',  'uses' => 'RestaurantController@getCourseDelete']);
            });

            Route::group(['prefix' => 'menu'], function () {
              Route::get('/',             ['as' => 'admin.food_menu.list',    'uses' => 'FoodController@getList']);
              Route::get('/add',          ['as' => 'admin.food_menu.add',     'uses' => 'FoodController@getAdd']);
              Route::post('/add',         ['as' => 'admin.food_menu.add',     'uses' => 'FoodController@postAdd']);
              Route::get('/edit/{id}',    ['as' => 'admin.food_menu.edit',    'uses' => 'FoodController@getEdit']);
              Route::post('/edit/{id}',   ['as' => 'admin.food_menu.edit',    'uses' => 'FoodController@postEdit']);
              Route::get('/delete/{id}',  ['as' => 'admin.food_menu.delete',  'uses' => 'FoodController@getDelete']);

              Route::post('/recommend',    ['as' => 'admin.food_menu.recommended',     'uses' => 'FoodController@recommend']);

            });

            Route::group(['prefix' => 'package'], function () {
              Route::get('/',             ['as' => 'admin.food_package.list',    'uses' => 'FoodController@getPackageList']);
              Route::get('/add',          ['as' => 'admin.food_package.add',     'uses' => 'FoodController@getPackageAdd']);
              Route::post('/add',         ['as' => 'admin.food_package.add',     'uses' => 'FoodController@postPackageAdd']);
              Route::get('/edit/{id}',    ['as' => 'admin.food_package.edit',    'uses' => 'FoodController@getPackageEdit']);
              Route::post('/edit/{id}',   ['as' => 'admin.food_package.edit',    'uses' => 'FoodController@postPackageEdit']);
              Route::get('/delete/{id}',  ['as' => 'admin.food_package.delete',  'uses' => 'FoodController@getPackageDelete']);
            });
        });


        //user
        Route::group(['prefix' => 'user'], function () {
          Route::get('/flexm/list',         ['as' => 'admin.flexm.user.list', 'uses' => 'UserController@getFlexmList','can' => 'view.user-list']);
          Route::get('/flexm/export',       ['as' => 'admin.flexm.user.export',    'uses' => 'UserController@exportFlexmExcel','can' => 'view.user-list']);
          Route::get('/download/permit',    ['as' => 'admin.user.download.permit',    'uses' => 'UserController@downloadPermit','can' => 'view.user-list']);
          
          Route::get('/select-role',        ['as' => 'admin.user.role-list', 'uses' => 'UserController@getRoleList','can' => 'view.user-list']);
          Route::get('/',                   ['as' => 'admin.user.list', 'uses' => 'UserController@getList','can' => 'view.user-list']);
          Route::get('/add',                ['as' => 'admin.user.add', 'uses' => 'UserController@getAdd','can' => 'create.user-add']);
          Route::post('/add',               ['as' => 'admin.user.add', 'uses' => 'UserController@postAdd','can' => 'create.user-add']);
          Route::get('/view/{id}',          ['as' => 'admin.user.view', 'uses' => 'UserController@getView','can' => 'view.user-list']);
          Route::get('/edit/{id}',          ['as' => 'admin.user.edit', 'uses' => 'UserController@getEdit','can' => 'update.user-edit']);
          Route::post('/edit/{id}',         ['as' => 'admin.user.update', 'uses' => 'UserController@postEdit','can' => 'update.user-edit']);
          Route::get('/delete/{id}',        ['as' => 'admin.user.delete', 'uses' => 'UserController@getDelete','can' => 'delete.user-delete']);
          Route::delete('/delete/{id}',     ['as' => 'admin.user.delete', 'uses' => 'UserController@postDelete','can' => 'delete.user-delete']);

          Route::get('/export',            ['as' => 'admin.user.export',    'uses' => 'UserController@exportExcel','can' => 'view.user-list']);

          Route::get('/module/{id}',        ['as' => 'admin.user.module.list',    'uses' => 'UserController@getTrainingList'])->where(['id' => '\d+']);

        });

        //menu
        Route::group(['prefix' => 'menu'], function () {
          Route::get('/',                   ['as' => 'admin.menu.list', 'uses' => 'MenuController@getList','can' => 'view.menu-list']);
          Route::get('/add',                ['as' => 'admin.menu.add', 'uses' => 'MenuController@getAdd','can' => 'create.menu-add']);
          Route::post('/add',               ['as' => 'admin.menu.add', 'uses' => 'MenuController@postAdd','can' => 'create.menu-add']);
          Route::get('/edit/{id}',          ['as' => 'admin.menu.edit', 'uses' => 'MenuController@getEdit','can' => 'update.menu-edit']);
          Route::post('/edit/{id}',         ['as' => 'admin.menu.update', 'uses' => 'MenuController@postEdit','can' => 'update.menu-edit']);
          // Route::get('/delete/{id}',        ['as' => 'admin.user.delete', 'uses' => 'UserController@getDelete']);
          // Route::delete('/delete/{id}',     ['as' => 'admin.user.delete', 'uses' => 'UserController@postDelete']);
          Route::get('/statistics/user',        ['as' => 'admin.menu.user', 'uses' => 'MenuController@getUser','can' => 'view.menu-list']);
          Route::get('/statistics/dormitory',   ['as' => 'admin.menu.dormitory', 'uses' => 'MenuController@getDormitory','can' => 'view.menu-list']);

          Route::get('/user',                   ['as' => 'admin.get.app_user', 'uses' => 'MenuController@getUserList','can' => 'view.menu-list']);
          Route::get('/dormitory',                   ['as' => 'admin.get.app_dormitory', 'uses' => 'MenuController@getDormitoryList','can' => 'view.menu-list']);
          
          Route::group(['prefix' => 'category'], function () {
            Route::get('/',                   ['as' => 'admin.menu.category.list', 'uses' => 'MenuCategoryController@getList','can' => 'view.menu-list']);
            Route::get('/add',                ['as' => 'admin.menu.category.add', 'uses' => 'MenuCategoryController@getAdd','can' => 'create.menu-add']);
            Route::post('/add',               ['as' => 'admin.menu.category.add', 'uses' => 'MenuCategoryController@postAdd','can' => 'create.menu-add']);
            Route::get('/edit/{id}',          ['as' => 'admin.menu.category.edit', 'uses' => 'MenuCategoryController@getEdit','can' => 'update.menu-edit']);
            Route::post('/edit/{id}',         ['as' => 'admin.menu.category.update', 'uses' => 'MenuCategoryController@postEdit','can' => 'update.menu-edit']);
            // Route::get('/delete/{id}',        ['as' => 'admin.user.delete', 'uses' => 'UserController@getDelete']);
            // Route::delete('/delete/{id}',     ['as' => 'admin.user.delete', 'uses' => 'UserController@postDelete']);
          });
        });

        // role
        Route::group(['prefix' => 'role'], function () {
          Route::get('/',             ['as' => 'admin.role.list',    'uses' => 'RoleController@getList','can' => 'view.role-list']);
          Route::get('/add',          ['as' => 'admin.role.add',     'uses' => 'RoleController@getAdd','can' => 'create.role-add']);
          Route::post('/add',         ['as' => 'admin.role.add',     'uses' => 'RoleController@postAdd','can' => 'create.role-add']);
          Route::get('/view/{id}',    ['as' => 'admin.role.view',    'uses' => 'RoleController@getView','can' => 'view.role-list']);
          Route::get('/edit/{id}',    ['as' => 'admin.role.edit',    'uses' => 'RoleController@getEdit','can' => 'update.role-edit']);
          Route::post('/edit/{id}',   ['as' => 'admin.role.edit',    'uses' => 'RoleController@postEdit','can' => 'update.role-edit']);
          Route::get('/delete/{id}',  ['as' => 'admin.role.delete',  'uses' => 'RoleController@getDelete','can' => 'delete.role-delete']);
        });

        // permission
        Route::group(['prefix' => 'permission'], function () {
          // Route::get('/',             ['as' => 'admin.permission.list',    'uses' => 'PermissionController@getList','can' => 'view.permission-list']);
          // Route::get('/add',          ['as' => 'admin.permission.add',     'uses' => 'PermissionController@getAdd','can' => 'create.permission-add']);
          // Route::post('/add',         ['as' => 'admin.permission.add',     'uses' => 'PermissionController@postAdd','can' => 'create.permission-add']);
          // Route::get('/edit/{id}',    ['as' => 'admin.permission.edit',    'uses' => 'PermissionController@getEdit','can' => 'update.permission-edit']);
          // Route::post('/edit/{id}',   ['as' => 'admin.permission.edit',    'uses' => 'PermissionController@postEdit','can' => 'update.permission-edit']);
          // Route::get('/delete/{id}',  ['as' => 'admin.permission.delete',  'uses' => 'PermissionController@getDelete','can' => 'delete.permission-delete']);
        });

        // advertisement
        Route::group(['prefix' => 'advertisement'], function () {
          Route::get('/',             ['as' => 'admin.advertisement.list',    'uses' => 'AdController@getList','can' => 'view.advertisement-list']);
          Route::get('/add',          ['as' => 'admin.advertisement.add',     'uses' => 'AdController@getAdd','can' => 'create.advertisement-add']);
          Route::post('/add',         ['as' => 'admin.advertisement.add',     'uses' => 'AdController@postAdd','can' => 'create.advertisement-add']);
          Route::get('/edit/{id}',    ['as' => 'admin.advertisement.edit',    'uses' => 'AdController@getEdit','can' => 'update.advertisement-edit']);
          Route::post('/edit/{id}',   ['as' => 'admin.advertisement.edit',    'uses' => 'AdController@postEdit','can' => 'update.advertisement-edit']);
          Route::get('/delete/{id}',  ['as' => 'admin.advertisement.delete',  'uses' => 'AdController@getDelete','can' => 'delete.advertisement-delete']);
          Route::get('/view/{id}',    ['as' => 'admin.advertisement.view',    'uses' => 'AdController@getView','can' => 'view.advertisement-list']);
          Route::post('/sponsor',         ['as' => 'admin.sponsor.add.ajax',     'uses' => 'AdController@postSponsor']);
          Route::get('/food',         ['as' => 'admin.advertisement.food',    'uses' => 'AdController@getIndex', 'is' => 'food-admin']);
          Route::post('/food',        ['as' => 'admin.advertisement.food',    'uses' => 'AdController@postIndex', 'is' => 'food-admin']);

          //plans
          Route::group(['prefix' => 'sponsor'], function () {
              Route::get('/',             ['as' => 'admin.sponsor.list',    'uses' => 'SponsorController@getList']);
              Route::get('/add',          ['as' => 'admin.sponsor.add',     'uses' => 'SponsorController@getAdd']);
              Route::post('/add',         ['as' => 'admin.sponsor.add',     'uses' => 'SponsorController@postAdd']);
              Route::get('/edit/{id}',    ['as' => 'admin.sponsor.edit',    'uses' => 'SponsorController@getEdit']);
              Route::post('/edit/{id}',   ['as' => 'admin.sponsor.edit',    'uses' => 'SponsorController@postEdit']);
              Route::get('/delete/{id}',  ['as' => 'admin.sponsor.delete',  'uses' => 'SponsorController@getDelete']);
              Route::get('/view/{id}',    ['as' => 'admin.sponsor.view',    'uses' => 'SponsorController@getView']);
          });

          //plans
          Route::group(['prefix' => 'plan'], function () {
              Route::get('/',             ['as' => 'admin.advertisement.plan.list',    'uses' => 'PlanController@getList', 'can' => 'view.ad_plan']);
              Route::get('/add',          ['as' => 'admin.advertisement.plan.add',     'uses' => 'PlanController@getAdd', 'can' => 'create.ad_plan']);
              Route::post('/add',         ['as' => 'admin.advertisement.plan.add',     'uses' => 'PlanController@postAdd', 'can' => 'create.ad_plan']);
              Route::get('/edit/{id}',    ['as' => 'admin.advertisement.plan.edit',    'uses' => 'PlanController@getEdit', 'can' => 'update.ad_plan']);
              Route::post('/edit/{id}',   ['as' => 'admin.advertisement.plan.edit',    'uses' => 'PlanController@postEdit', 'can' => 'update.ad_plan']);
              Route::get('/delete/{id}',  ['as' => 'admin.advertisement.plan.delete',  'uses' => 'PlanController@getDelete', 'can' => 'delete.ad_plan']);
          });

          //invoices
          Route::group(['prefix' => 'invoice'], function () {
              Route::get('/',             ['as' => 'admin.invoice.list',    'uses' => 'InvoiceController@getList','can' => 'view.invoice-list']);
              Route::get('/add',          ['as' => 'admin.invoice.add',     'uses' => 'InvoiceController@getAdd','can' => 'create.invoice-add']);
              Route::post('/add',         ['as' => 'admin.invoice.add',     'uses' => 'InvoiceController@postAdd','can' => 'create.invoice-add']);
              Route::get('/view/{id}',    ['as' => 'admin.invoice.view',    'uses' => 'InvoiceController@getEdit','can' => 'view.invoice-list']);
              Route::post('/edit/{id}',   ['as' => 'admin.invoice.edit',    'uses' => 'InvoiceController@postEdit','can' => 'update.invoice-edit']);
              Route::post('/paid',        ['as' => 'admin.invoice.paid',    'uses' => 'InvoiceController@statusPaid','can' => 'view.invoice-list']);
              Route::get('/export',       ['as' => 'admin.invoice.export',  'uses' => 'InvoiceController@export','can' => 'view.invoice-list']);
          });

        });

        // course
        Route::group(['prefix' => 'course'], function () {
          Route::get('/',             ['as' => 'admin.course.list',    'uses' => 'CourseController@getList','can' => 'view.course-list']);
          Route::get('/add',          ['as' => 'admin.course.add',     'uses' => 'CourseController@getAdd','can' => 'create.course-add']);
          Route::post('/add',         ['as' => 'admin.course.add',     'uses' => 'CourseController@postAdd','can' => 'create.course-add']);
          Route::get('/edit/{id}',    ['as' => 'admin.course.edit',    'uses' => 'CourseController@getEdit','can' => 'update.course-edit']);
          Route::post('/edit/{id}',   ['as' => 'admin.course.edit',    'uses' => 'CourseController@postEdit','can' => 'update.course-edit']);
          Route::get('/delete/{id}',  ['as' => 'admin.course.delete',  'uses' => 'CourseController@getDelete','can' => 'delete.course-delete']);
          Route::group(['prefix' => 'content'], function () {
            Route::get('/',             ['as' => 'admin.content.list',    'uses' => 'CourseController@getCList','can' => 'view.content-list']);
            Route::get('/add',          ['as' => 'admin.content.add',     'uses' => 'CourseController@getCAdd','can' => 'create.content-add']);
            Route::post('/add',         ['as' => 'admin.content.add',     'uses' => 'CourseController@postCAdd','can' => 'create.content-add']);
            Route::get('/edit/{id}',    ['as' => 'admin.content.edit',    'uses' => 'CourseController@getCEdit','can' => 'update.content-edit']);
            Route::post('/edit/{id}',   ['as' => 'admin.content.edit',    'uses' => 'CourseController@postCEdit','can' => 'update.content-edit']);
            Route::get('/delete/{id}',  ['as' => 'admin.content.delete',  'uses' => 'CourseController@getCDelete','can' => 'delete.content-delete']);
          });

          Route::get('/joinees',             ['as' => 'admin.course.joinees',    'uses' => 'CourseController@getJoinees','can' => 'view.course-list']);


        });

        // category
        Route::group(['prefix' => 'category'], function () {
          Route::get('/',             ['as' => 'admin.category.list',    'uses' => 'CategoryController@getList','can' => 'view.emergency-list']);
          Route::get('/add',          ['as' => 'admin.category.add',     'uses' => 'CategoryController@getAdd','can' => 'view.emergency-list']);
          Route::post('/add',         ['as' => 'admin.category.add',     'uses' => 'CategoryController@postAdd','can' => 'view.emergency-list']);
          Route::get('/edit/{id}',    ['as' => 'admin.category.edit',    'uses' => 'CategoryController@getEdit','can' => 'view.emergency-list']);
          Route::post('/edit/{id}',   ['as' => 'admin.category.edit',    'uses' => 'CategoryController@postEdit','can' => 'view.emergency-list']);
          Route::get('/delete/{id}',  ['as' => 'admin.category.delete',  'uses' => 'CategoryController@getDelete','can' => 'view.emergency-list']);
        });

        // emergency
        Route::group(['prefix' => 'emergency'], function () {
          Route::get('/',             ['as' => 'admin.emergency.list',    'uses' => 'EmergencyController@getList','can' => 'view.emergency-list']);
          Route::get('/add',          ['as' => 'admin.emergency.add',     'uses' => 'EmergencyController@getAdd','can' => 'create.emergency-add']);
          Route::post('/add',         ['as' => 'admin.emergency.add',     'uses' => 'EmergencyController@postAdd','can' => 'create.emergency-add']);
          Route::get('/edit/{id}',    ['as' => 'admin.emergency.edit',    'uses' => 'EmergencyController@getEdit','can' => 'update.emergency-edit']);
          Route::post('/edit/{id}',   ['as' => 'admin.emergency.edit',    'uses' => 'EmergencyController@postEdit','can' => 'update.emergency-edit']);
          Route::get('/delete/{id}',  ['as' => 'admin.emergency.delete',  'uses' => 'EmergencyController@getDelete','can' => 'delete.emergency-delete']);
        });

        // option
        Route::group(['prefix' => 'option'], function () {
          Route::get('/',             ['as' => 'admin.option.list',    'uses' => 'OptionController@getList']);
          Route::get('/add',          ['as' => 'admin.option.add',     'uses' => 'OptionController@getAdd']);
          Route::post('/add',         ['as' => 'admin.option.add',     'uses' => 'OptionController@postAdd']);
          Route::get('/edit/{id}',    ['as' => 'admin.option.edit',    'uses' => 'OptionController@getEdit']);
          Route::post('/edit/{id}',   ['as' => 'admin.option.edit',    'uses' => 'OptionController@postEdit']);
          Route::get('/delete/{id}',  ['as' => 'admin.option.delete',  'uses' => 'OptionController@getDelete']);
        });

        // page
        Route::group(['prefix' => 'page'], function () {
          Route::get('/',             ['as' => 'admin.page.list',    'uses' => 'PageController@getList','can' => 'view.page-list']);
          Route::get('/add',          ['as' => 'admin.page.add',     'uses' => 'PageController@getAdd','can' => 'create.page-add']);
          Route::post('/add',         ['as' => 'admin.page.add',     'uses' => 'PageController@postAdd','can' => 'create.page-add']);
          Route::get('/edit/{id}',    ['as' => 'admin.page.edit',    'uses' => 'PageController@getEdit','can' => 'update.page-edit']);
          Route::post('/edit/{id}',   ['as' => 'admin.page.edit',    'uses' => 'PageController@postEdit','can' => 'update.page-edit']);
          Route::get('/delete/{id}',  ['as' => 'admin.page.delete',  'uses' => 'PageController@getDelete']);//,'can' => 'delete.page-list'

          Route::get('/mwc',          ['as' => 'admin.mwc.list',     'uses' => 'PageController@getMWC','can' => 'view.page-list']);
          Route::post('/mwc',         ['as' => 'admin.mwc.list',     'uses' => 'PageController@postMWC','can' => 'view.page-list']);

          Route::get('/links',          ['as' => 'admin.links.list',     'uses' => 'PageController@getLinks','can' => 'view.page-list']);
          Route::post('/links',         ['as' => 'admin.links.list',     'uses' => 'PageController@postLinks','can' => 'view.page-list']);
          Route::get('/flexm',         ['as' => 'admin.flexm.pages',    'uses' => 'PageController@getFlexm','can' => 'view.page-list']);
        });

        // forum
        Route::group(['prefix' => 'forum'], function () {
          Route::get('/',             ['as' => 'admin.forum.list',    'uses' => 'ForumController@getList','can' => 'view.forum-list']);
          Route::get('/add',          ['as' => 'admin.forum.add',     'uses' => 'ForumController@getAdd','can' => 'view.forum-add']);
          Route::post('/add',         ['as' => 'admin.forum.add',     'uses' => 'ForumController@postAdd','can' => 'view.forum-add']);
          Route::get('/view/{id}',    ['as' => 'admin.forum.view',    'uses' => 'ForumController@getView','can' => 'view.forum-list']);
          Route::post('/view/{id}',   ['as' => 'admin.forum.reply',    'uses' => 'ForumController@postReply','can' => 'view.forum-list']);
          Route::get('/delete/{id}',  ['as' => 'admin.forum.delete',  'uses' => 'ForumController@getDelete','can' => 'view.forum-list']);
          Route::get('/edit/{id}',    ['as' => 'admin.forum.edit',    'uses' => 'ForumController@getEdit','can' => 'update.forum-edit']);
          Route::post('/edit/{id}',   ['as' => 'admin.forum.edit',    'uses' => 'ForumController@postEdit','can' => 'update.forum-edit']);

          Route::get('/update/status/{id}',    ['as' => 'admin.forum.unreport',    'uses' => 'ForumController@unReport','can' => 'update.forum-edit']);

          Route::get('/comment/delete/{id}',  ['as' => 'admin.comments.delete',  'uses' => 'ForumController@getCommentsDelete']);
        });

        // topic
        Route::group(['prefix' => 'topic'], function () {
          Route::get('/',             ['as' => 'admin.topic.list',    'uses' => 'TopicController@getList','can' => 'view.topic-list']);
          Route::get('/add',          ['as' => 'admin.topic.add',     'uses' => 'TopicController@getAdd','can' => 'create.topic-add']);
          Route::post('/add',         ['as' => 'admin.topic.add',     'uses' => 'TopicController@postAdd','can' => 'create.topic-add']);
          Route::get('/edit/{id}',    ['as' => 'admin.topic.edit',    'uses' => 'TopicController@getEdit','can' => 'update.topic-edit']);
          Route::post('/edit/{id}',   ['as' => 'admin.topic.edit',    'uses' => 'TopicController@postEdit','can' => 'update.topic-edit']);
          Route::get('/delete/{id}',  ['as' => 'admin.topic.delete',  'uses' => 'TopicController@getDelete']);//,'can' => 'delete.topic-list'
        });

        // services
        Route::group(['prefix' => 'services'], function () {
          Route::get('/',             ['as' => 'admin.services.list',    'uses' => 'ServicesController@getList','can' => 'view.services-list']);
          Route::get('/add',          ['as' => 'admin.services.add',     'uses' => 'ServicesController@getAdd','can' => 'view.services-list']);
          Route::post('/add',         ['as' => 'admin.services.add',     'uses' => 'ServicesController@postAdd','can' => 'view.services-list']);
          Route::get('/view/{id}',    ['as' => 'admin.services.view',    'uses' => 'ServicesController@getView','can' => 'view.services-list']);
          Route::get('/edit/{id}',    ['as' => 'admin.services.edit',    'uses' => 'ServicesController@getEdit','can' => 'view.services-list']);
          Route::post('/edit/{id}',   ['as' => 'admin.services.edit',    'uses' => 'ServicesController@postEdit','can' => 'view.services-list']);
          Route::get('/delete/{id}',  ['as' => 'admin.services.delete',  'uses' => 'ServicesController@getDelete','can' => 'view.services-list']);

          Route::group(['prefix' => 'comments'], function () {
            Route::get('/',             ['as' => 'admin.services.comments.list',    'uses' => 'ServicesController@getCommentsList','can' => 'view.services-list']);
            Route::get('/view/{id}',    ['as' => 'admin.services.comments.view',    'uses' => 'ServicesController@getCommentsView','can' => 'view.services-list']);
            Route::get('/edit/{id}',    ['as' => 'admin.services.comments.edit',    'uses' => 'ServicesController@getCommentsEdit','can' => 'view.services-list']);
            Route::post('/edit/{id}',   ['as' => 'admin.services.comments.edit',    'uses' => 'ServicesController@postCommentsEdit','can' => 'view.services-list']);
            Route::get('/delete/{id}',  ['as' => 'admin.services.comments.delete',  'uses' => 'ServicesController@getCommentsDelete','can' => 'view.services-list']);
          });
        });

        // maintenance
        Route::group(['prefix' => 'dormitory'], function () {
          Route::get('/',             ['as' => 'admin.dormitory.list',    'uses' => 'DormitoryController@getList','can' => 'view.maintenance-list']);
          Route::get('/add',          ['as' => 'admin.dormitory.add',     'uses' => 'DormitoryController@getAdd','can' => 'create.maintenance-add']);
          Route::post('/add',         ['as' => 'admin.dormitory.add',     'uses' => 'DormitoryController@postAdd','can' => 'create.maintenance-add']);
          Route::get('/edit/{id}',    ['as' => 'admin.dormitory.edit',    'uses' => 'DormitoryController@getEdit','can' => 'update.maintenance-edit']);
          Route::get('/view/{id}',    ['as' => 'admin.dormitory.view',    'uses' => 'DormitoryController@getView','can' => 'view.maintenance-list']);
          Route::post('/edit/{id}',   ['as' => 'admin.dormitory.edit',    'uses' => 'DormitoryController@postEdit','can' => 'update.maintenance-edit']);
          Route::get('/delete/{id}',  ['as' => 'admin.dormitory.delete',  'uses' => 'DormitoryController@getDelete','can' => 'delete.maintenance-delete']);
        });

        // maintenance
        Route::group(['prefix' => 'maintenance'], function () {
          Route::get('/',             ['as' => 'admin.maintenance.list',    'uses' => 'MaintenanceController@getList','can' => 'view.maintenance-list']);
          Route::get('/add',          ['as' => 'admin.maintenance.add',     'uses' => 'MaintenanceController@getAdd','can' => 'view.maintenance-list']);
          Route::post('/add',         ['as' => 'admin.maintenance.add',     'uses' => 'MaintenanceController@postAdd','can' => 'view.maintenance-list']);
          Route::get('/edit/{id}',    ['as' => 'admin.maintenance.edit',    'uses' => 'MaintenanceController@getEdit','can' => 'view.maintenance-list']);
          Route::get('/view/{id}',    ['as' => 'admin.maintenance.view',    'uses' => 'MaintenanceController@getView','can' => 'view.maintenance-list']);
          Route::post('/edit/{id}',   ['as' => 'admin.maintenance.edit',    'uses' => 'MaintenanceController@postEdit','can' => 'view.maintenance-list']);
          Route::get('/delete/{id}',  ['as' => 'admin.maintenance.delete',  'uses' => 'MaintenanceController@getDelete','can' => 'view.maintenance-list']);

          Route::get('/export/{id}',    ['as' => 'admin.maintenance.export',    'uses' => 'MaintenanceController@exportPDF','can' => 'view.maintenance-list']);
        });

        //word
        Route::group(['prefix' => 'words'], function () {
          Route::get('/',                   ['as' => 'admin.words.list', 'uses' => 'ProfanityController@getList']);
          Route::get('/add',                ['as' => 'admin.words.add', 'uses' => 'ProfanityController@getAdd']);
          Route::post('/add',               ['as' => 'admin.words.add', 'uses' => 'ProfanityController@postAdd']);
          Route::get('/edit/{id}',          ['as' => 'admin.words.edit', 'uses' => 'ProfanityController@getEdit']);
          Route::post('/edit/{id}',         ['as' => 'admin.words.update', 'uses' => 'ProfanityController@postEdit']);
          Route::get('/delete/{id}',        ['as' => 'admin.words.delete', 'uses' => 'ProfanityController@getDelete']);
          Route::delete('/delete/{id}',     ['as' => 'admin.words.delete', 'uses' => 'ProfanityController@postDelete']);
        });

        // feedback
        Route::group(['prefix' => 'feedback'], function () {
          Route::get('/',             ['as' => 'admin.feedback.list',    'uses' => 'FeedbackController@getList','can' => 'view.feedback-list']);
          Route::get('/reply/{id}',    ['as' => 'admin.feedback.reply',    'uses' => 'FeedbackController@getReply','can' => 'view.feedback-list']);
          Route::post('/reply/{id}',   ['as' => 'admin.feedback.reply',    'uses' => 'FeedbackController@postReply','can' => 'view.feedback-list']);
          Route::get('/view/{id}',    ['as' => 'admin.feedback.view',    'uses' => 'FeedbackController@getView','can' => 'view.feedback-list']);
          Route::get('/delete/{id}',  ['as' => 'admin.feedback.delete',  'uses' => 'FeedbackController@getDelete','can' => 'view.feedback-list']);
        });

        // contact
        Route::group(['prefix' => 'contact'], function () {
          Route::get('/',             ['as' => 'admin.contact.list',    'uses' => 'ContactController@getList','can' => 'view.feedback-list']);
          Route::get('/reply/{id}',    ['as' => 'admin.contact.reply',    'uses' => 'ContactController@getReply','can' => 'view.feedback-list']);
          Route::post('/reply/{id}',   ['as' => 'admin.contact.reply',    'uses' => 'ContactController@postReply','can' => 'view.feedback-list']);
          Route::get('/view/{id}',    ['as' => 'admin.contact.view',    'uses' => 'ContactController@getView','can' => 'view.feedback-list']);
          // Route::post('/view/{id}',   ['as' => 'admin.contact.view',    'uses' => 'ContactController@postView','can' => 'view.feedback-list']);
          Route::get('/delete/{id}',  ['as' => 'admin.contact.delete',  'uses' => 'ContactController@getDelete','can' => 'view.feedback-list']);
        });

        // search
        Route::group(['prefix' => 'search'], function () {
          Route::get('/',             ['as' => 'admin.search.list',    'uses' => 'SearchController@getList','can' => 'view.search-list']);
          Route::get('/delete/{id}',  ['as' => 'admin.search.delete',  'uses' => 'SearchController@getDelete','can' => 'view.search-list']);
        });

        //training
        Route::group(['prefix' => 'training'], function () {
          Route::get('/',             ['as' => 'admin.training.list',    'uses' => 'TrainingController@getList','can' => 'view.course-list']);
          Route::get('/report',       ['as' => 'admin.training.report',  'uses' => 'TrainingController@getReport','can' => 'view.course-list']);
          Route::get('/add',          ['as' => 'admin.training.add',     'uses' => 'TrainingController@getAdd','can' => 'view.course-list']);
          Route::post('/add',         ['as' => 'admin.training.add',     'uses' => 'TrainingController@postAdd','can' => 'view.course-list']);
          Route::get('/edit/{id}',    ['as' => 'admin.training.edit',    'uses' => 'TrainingController@getEdit','can' => 'view.course-list']);
          Route::post('/edit/{id}',   ['as' => 'admin.training.edit',    'uses' => 'TrainingController@postEdit','can' => 'view.course-list']);
          Route::get('/delete/{id}',  ['as' => 'admin.training.delete',  'uses' => 'TrainingController@getDelete','can' => 'view.course-list']);

          Route::group(['prefix' => 'review'], function () {

            Route::get('/list/{id}',    ['as' => 'admin.training.review.list',    'uses' => 'TrainingController@getReviewList']);
            Route::get('/add',          ['as' => 'admin.training.review.add',     'uses' => 'TrainingController@getReviewAdd']);
            Route::post('/add',         ['as' => 'admin.training.review.add',     'uses' => 'TrainingController@postReviewAdd']);
            // Route::get('/edit/{id}',    ['as' => 'admin.training.edit',    'uses' => 'TrainingController@getEdit']);
            // Route::post('/edit/{id}',   ['as' => 'admin.training.edit',    'uses' => 'TrainingController@postEdit']);
            Route::get('/delete/{id}',  ['as' => 'admin.training.review.delete',  'uses' => 'TrainingController@getReviewDelete']);

          });

          // Route::group(['prefix' => 'read'], function () {
          //   Route::get('/list/{id}',         ['as' => 'admin.training.read.list',    'uses' => 'TrainingController@getReadList']);
          // });
        });

        //incident
        Route::group(['prefix' => 'incident'], function () {
          Route::get('/',             ['as' => 'admin.incident.list',    'uses' => 'IncidentController@getList','can' => 'view.incident-list']);
          // Route::get('/add',          ['as' => 'admin.incident.add',     'uses' => 'IncidentController@getAdd']);
          // Route::post('/add',         ['as' => 'admin.incident.add',     'uses' => 'IncidentController@postAdd']);
          Route::get('/view/{id}',    ['as' => 'admin.incident.view',    'uses' => 'IncidentController@getView','can' => 'view.incident-list']);
		  Route::get('/export/{id}',    ['as' => 'admin.incident.export',    'uses' => 'IncidentController@exportPDF','can' => 'view.incident-list']);
          // Route::post('/edit/{id}',   ['as' => 'admin.incident.edit',    'uses' => 'IncidentController@postEdit']);
          Route::get('/delete/{id}',  ['as' => 'admin.incident.delete',  'uses' => 'IncidentController@getDelete','can' => 'view.incident-list']);
        });

        //Report
        // Route::group(['prefix' => 'report'], function () {
        //   Route::get('/attendence',    ['as' => 'admin.report.attendence',    'uses' => 'ReportController@getAttendence']);
        //   Route::get('/attendence/download',    ['as' => 'download.attendence',    'uses' => 'ReportController@downloadAttendence']);
        //   Route::get('/add',          ['as' => 'admin.report.add',     'uses' => 'ReportController@getAdd']);
        //
        // });

        //settings
        Route::group(['prefix' => 'settings'], function () {
          Route::get('/',             ['as' => 'admin.settings.show',    'uses' => 'SettingsController@getIndex', 'can' => 'view.settings-general']);
          Route::post('/add',         ['as' => 'admin.settings.update',     'uses' => 'SettingsController@postIndex', 'can' => 'view.settings-general']);
        });

        // feedback
        Route::group(['prefix' => 'notification'], function () {
            Route::get('/',                  ['as' => 'admin.notification.add',     'uses' => 'NotificationController@getAdd', 'can' => 'view.send-notification']);
            Route::post('/',                 ['as' => 'admin.notification.add',    'uses' => 'NotificationController@postAdd', 'can' => 'view.send-notification']);

        });
    });
});
