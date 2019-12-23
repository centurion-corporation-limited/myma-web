<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="{{  static_file('manifest.json') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <!-- Bootstrap -->
    <link href="{{  static_file('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="{{  static_file('css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{  static_file('assets/admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{  static_file('assets/admin/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="{{  static_file('assets/admin/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet"/>
    <!-- JQVMap -->
    <!-- <link href="{{  static_file('assets/admin/vendors/jqvmap/dist/jqvmap.min.css') }}" rel="stylesheet"> -->

    <!-- bootstrap-wysiwyg -->
    <link href="{{  static_file('assets/admin/vendors/google-code-prettify/bin/prettify.min.css') }}" rel="stylesheet">
    <!-- Select2 -->
    <link href="{{  static_file('assets/admin/vendors/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <!-- Switchery -->
    <link href="{{  static_file('assets/admin/vendors/switchery/dist/switchery.min.css') }}" rel="stylesheet">
    <!-- starrr -->
    <link href="{{  static_file('assets/admin/vendors/starrr/dist/starrr.css') }}" rel="stylesheet">

    <!-- bootstrap-daterangepicker -->
    <link href="{{  static_file('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

    <!-- bootstrap-fancy -->
    <link href="{{  static_file('js/plugins/bootstrap-fancyfile-master/css/bootstrap-fancyfile.min.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->

    <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.bootstrap.min.css" />
    <link href="{{  static_file('assets/admin/css/custom.min.css') }}" rel="stylesheet">
    <link href="{{  static_file('css/responsive.css') }}" rel="stylesheet">

    <script src="https://www.gstatic.com/firebasejs/4.13.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/4.13.0/firebase-messaging.js"></script>
<!-- <script>
  // Initialize Firebase
  var config = {
    apiKey: "AIzaSyD3F8J2jwnGY7zTmDdhgMu6ebJJ5CSAuHM",
    authDomain: "myma-1520248374001.firebaseapp.com",
    databaseURL: "https://myma-1520248374001.firebaseio.com",
    projectId: "myma-1520248374001",
    storageBucket: "myma-1520248374001.appspot.com",
    messagingSenderId: "185969502127"
  };
  firebase.initializeApp(config);

  const messaging = firebase.messaging();
  messaging.usePublicVapidKey("BGpkX8Tf4yTLmUoCNoMF6tay_DkN9CPb8IIuPy2VSiOxTE4cKq7NDsjuLFosB0yNxTkqo0tIlzPerNSLPM3W7Zg");
  messaging.requestPermission().then(function() {
      console.log('Notification permission granted.');
      // TODO(developer): Retrieve an Instance ID token for use with FCM.
      // ...
  }).catch(function(err) {
      console.log('Unable to get permission to notify.', err);
  });

</script> -->

    @yield('styles')
    <!-- <script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'bn,en,ta,zh-CN', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, multilanguagePage: true}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<style>
.goog-te-gadget-simple{ margin-top: 20px;}
</style> -->
  </head>
  @if(Auth::guest())
  <body class="login">

    @yield('content')
  @else
  <body class="nav-sm">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="{{ route('admin.dashboard') }}" class="site_title"><!--<i class="fa fa-paw"></i> <span>{{ config('app.name') }}</span>--> <img src="{{ static_file('images/img-logo.png') }}" alt=""></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <!-- <div class="profile_pic">
                <img src="{{  static_file('assets/admin/images/img.jpg') }}" alt="..." class="img-circle profile_img">
              </div> -->
              <!-- <div class="profile_info">
                <span>Welcome,</span>
                <h2>Admin</h2>
              </div> -->
            </div>
            <!-- /menu profile quick info -->

            <br />
            <?php
              $user = Auth::user();
              $perm = $user->getPermissions();

                $is_inapp = $is_instore = false;
                if($user){
                    $merchant = App\Models\Merchant::where('user_id', $user->id)->first();
                    if($merchant){
                      if($merchant->type == 'inapp'){
                        $is_inapp = true;
                      }
                      if($merchant->type == 'instore'){
                        $is_instore = true;
                      }
                    }
                }
                
                $menus = \App\Models\Menu::where('type', 'jtc')->get();

            ?>
            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <ul class="nav side-menu">
                  @if($user->hasRole('admin') || $user->hasRole('food-admin') || $user->hasRole('spuul') || $user->hasRole('training'))
                  <li><a href="{{ url('/') }}"><i class="fa fa-home"></i> Dashboard </a></li>
                  @endif
                  @if($user->can('create.user-add|view.user-list'))
                  <li><a><i class="fa fa-user"></i> User Management <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if(@$perm['role-add'])
                      <li><a href="{{ route('admin.role.add') }}">Add Role</a></li>
                      @endif
                      @if(@$perm['role-list'])
                      <li><a href="{{ route('admin.role.list') }}">List Role</a></li>
                      @endif
                      @if(@$perm['permission-list'])
                      {{--<li><a href="{{ route('admin.permission.list') }}">List Permission</a></li>--}}
                      @endif
                      @if(@$perm['permission-add'])
                      {{--<li><a href="{{ route('admin.permission.list') }}">Add Permission</a></li>--}}
                      @endif
                      @if($user->can('create.user-add'))
                      <li><a href="{{ route('admin.user.add') }}">Add User</a></li>
                      @endif
                      @if($user->can('view.user-list'))
                      <li><a href="{{ route('admin.user.role-list') }}">List User</a></li>
                      @if(!$user->hasRole('food-admin') && $user->can('view.user-list'))
                      <li><a href="{{ route('admin.flexm.user.list') }}">Flexm Cron User List</a></li>
                      @endif
                      @endif
                    </ul>
                  </li>
                  @endif

                  @if($user->can('create.advertisement-add|view.advertisement-list'))
                  <li><a><i class="fa fa-desktop"></i> Advertisement <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('view.ad_plan'))
                        <li><a href="{{ route('admin.advertisement.plan.list') }}">Pricing plans</a></li>
                        <li><a href="{{ route('admin.invoice.list') }}">Invoices</a></li>
                      @endif
                      @if($user->can('create.advertisement-add'))
                      <li><a href="{{ route('admin.advertisement.add') }}">Add New</a></li>
                      @endif
                      @if($user->can('view.advertisement-list'))
                      <li><a href="{{ route('admin.advertisement.list') }}">List</a></li>
                      @endif
                      <li><a href="{{ route('admin.sponsor.list') }}">Sponsor List</a></li>
                      @if($user->hasRole('food-admin'))
                      <li><a href="{{ route('admin.advertisement.food') }}">Bottom Ad</a></li>
                      @endif
                    </ul>
                  </li>
                  @endif

                  @if($user->can('create.course-add|view.course-list|create.content-add|view.content-list'))
                  <li><a><i class="fa fa-book"></i> Course <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('create.course-add'))
                      <li><a href="{{ route('admin.course.add') }}">Add New</a></li>
                      @endif
                      @if($user->can('view.course-list'))
                      <li><a href="{{ route('admin.course.list') }}">List</a></li>
                      @endif
                      @if($user->can('view.content-list'))
                      <li><a href="{{ route('admin.content.list') }}">Content List</a></li>
                      @endif
                      @if($user->can('create.content-add'))
                      <!-- <li><a href="{{ route('admin.content.add') }}">Add Course Content</a></li> -->
                      @endif
                    </ul>
                  </li>
                  @endif

                  @if($user->can('create.menu-add|view.menu-list'))
                  <li><a><i class="fa fa-newspaper-o"></i> Menu Management <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('view.menu-list'))
                      <li><a href="{{ route('admin.menu.list') }}">List</a></li>
                      <li><a href="{{ route('admin.menu.category.list') }}">List Category</a></li>
                      @endif
                      @if($user->can('create.menu-add'))
                      <!-- <li><a href="{{ route('admin.menu.add') }}">Add New</a></li> -->
                      @endif
                    </ul>
                  </li>
                  @endif

                  @if($user->can('create.page-add|view.page-list'))
                  <li><a><i class="fa fa-clone"></i> Pages <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('create.page-add'))
                      <li><a href="{{ route('admin.page.add') }}">Add New</a></li>
                      @endif
                      @if($user->can('view.page-list'))
                      <li><a href="{{ route('admin.page.list') }}">List</a></li>
                      @endif
                      @if($user->can('view.page-list'))
                      <li><a href="{{ route('admin.mwc.list') }}">Manage MWC</a></li>
                      @endif

                      @if($user->can('view.page-list'))
                      <li><a href="{{ route('admin.links.list') }}">Manage Links</a></li>
                      @endif
                      @if($user->can('view.page-list'))
                      <li><a href="{{ route('admin.flexm.pages') }}">Manage Flexm Content</a></li>
                      @endif
                    </ul>
                  </li>
                  @endif

                  @if($user->can('view.mom-category-list') || $user->can('view.mom-topic-list'))
                  <li><a><i class="fa fa-newspaper-o"></i> MOM <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('add.mom-category-list'))
                      <li><a href="{{ route('admin.mom.category.add') }}">Add Category</a></li>
                      @endif
                      <li><a href="{{ route('admin.mom.category.list') }}">List Category</a></li>
                      @if($user->can('add.mom-topic-list'))
                      <li><a href="{{ route('admin.mom.topic.add') }}">Add Topic</a></li>
                      @endif
                      <li><a href="{{ route('admin.mom.topic.list') }}">List Topic</a></li>

                    </ul>
                  </li>
                  @endif
                
                  @if($user->can('view.jtc-list') )
                  <li><a><i class="fa fa-newspaper-o"></i> CMS <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @foreach($menus as $menu)
                        <li class="cms"><a>{{ $menu->name }}</a>
                          <ul class="nav child_menu">
                            <li><a href="{{ route('admin.jtc.centers.add', ['type' => $menu->slug]) }}">Add Main Category</a></li>
                            <li><a href="{{ route('admin.jtc.centers.list', ['type' => $menu->slug]) }}">List Main Category</a></li>
                            <li><a href="{{ route('admin.jtc.category.add', ['type' => $menu->slug]) }}">Add Sub-Category-1</a></li>
                            <li><a href="{{ route('admin.jtc.category.list', ['type' => $menu->slug]) }}">List Sub-Category-1</a></li>
                            <li><a href="{{ route('admin.jtc.event.add', ['type' => $menu->slug]) }}">Add Sub-Category-2</a></li>
                            <li><a href="{{ route('admin.jtc.event.list', ['type' => $menu->slug]) }}">List Sub-Category-2</a></li>
                            <li><a href="{{ route('admin.jtc.detail.add', ['menu_type' => $menu->slug]) }}">Add Detail</a></li>
                            <li><a href="{{ route('admin.jtc.detail.list', ['menu_type' => $menu->slug]) }}">List Detail</a></li>
                          </ul>
                        </li>
                      @endforeach
                    </ul>
                  </li>
                  @endif
                  
                  @if($user->can('create.services-add|view.services-list'))
                  <li><a><i class="fa fa-newspaper-o"></i> Embassy/Event & Attraction <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('create.services-add'))
                      <li><a href="{{ route('admin.services.add') }}">Add New</a></li>
                      @endif
                      @if($user->can('view.services-list'))
                      <li><a href="{{ route('admin.services.list') }}">List</a></li>
                      @endif
                    </ul>
                  </li>
                  @endif
                  @if($user->can('create.maintenance-add|view.maintenance-list|view.dormitory-list'))
                  <li><a><i class="fa fa-wrench"></i> Dormitory <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('view.dormitory-list'))
                        <li><a href="{{ route('admin.dormitory.list') }}">List</a></li>
                      @endif
                      @if($user->can('view.maintenance-list'))
                      <li><a href="{{ route('admin.maintenance.list') }}">Maintenance</a></li>
                      @endif
                    </ul>
                  </li>
                  @endif
                  @if($user->can('create.option-add|view.option-list'))
                  <li><a><i class="fa fa-desktop"></i> Emergency Numbers <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('create.option-add'))
                      <li><a href="{{ route('admin.option.add') }}">Add New</a></li>
                      @endif
                      @if($user->can('view.option-list'))
                      <li><a href="{{ route('admin.option.list') }}">List</a></li>
                      @endif
                    </ul>
                  </li>
                  @endif
                  @if($user->can('create.topic-add|view.topic-list'))
                  <li><a><i class="fa fa-desktop"></i> Forum <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('view.topic-list'))
                      <li><a href="{{ route('admin.topic.list') }}">List Topics</a></li>
                      @endif
                      {{-- @if($user->can('view.topic-list'))
                      @endif --}}
                      <li><a href="{{ route('admin.words.list') }}">Bad Word List</a></li>
                    </ul>
                  </li>
                  @endif
                  @if($user->can('create.feedback-add|view.feedback-list'))
                  <li><a><i class="fa fa-comments"></i> Feedback <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('view.feedback-list'))
                      <li><a href="{{ route('admin.feedback.list') }}">List</a></li>
                      <li><a href="{{ route('admin.feedback.list', ['type' => 'mom']) }}">List(MOM)</a></li>
                      @endif
                       <li><a href="{{ route('admin.contact.list') }}">Contact Requests</a></li>
                    </ul>
                  </li>
                  @endif
                  @if($user->can('create.search-add|view.search-list'))
                  <li><a><i class="fa fa-search"></i> Search <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('view.search-list'))
                      <li><a href="{{ route('admin.search.list') }}">List</a></li>
                      @endif
                      {{-- <li><a href="{{ route('admin.feedback.add') }}">Add New</a></li> --}}
                    </ul>
                  </li>
                  @endif
                  @if($user->can('create.incident-add|view.incident-list'))
                  <li><a><i class="fa fa-tasks"></i> Incident Report <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('view.incident-list'))
                      <li><a href="{{ route('admin.incident.list') }}">List</a></li>
                      @endif
                    </ul>
                  </li>
                  @endif

                  @if($user->can('create.emergency-add|view.emergency-list'))
                  <li><a><i class="fa fa-phone"></i> Emergency Numbers <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('view.emergency-list'))
                      <li><a href="{{ route('admin.category.list') }}">List Category</a></li>
                      @endif
                      @if($user->can('create.emergency-add'))
                      <!-- <li><a href="{{ route('admin.category.add') }}">Add Category</a></li> -->
                      @endif
                      @if($user->can('view.emergency-list'))
                      <li><a href="{{ route('admin.emergency.list') }}">List Numbers</a></li>
                      @endif
                      @if($user->can('create.emergency-add'))
                      <!-- <li><a href="{{ route('admin.emergency.add') }}">Add Numbers</a></li> -->
                      @endif
                    </ul>
                  </li>
                  @endif

                  @if($user->hasRole('food-admin') || $user->hasRole('restaurant-owner-single') || $user->hasRole('restaurant-owner-catering') )
                  <li><a><i class="fa fa-coffee"></i> Food <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                            <li><a href="{{ route('admin.order.list') }}">List Orders</a></li>
                            @if($user->hasRole('food-admin'))
                            <!-- <li><a href="{{ route('admin.subscriptions.list') }}">List Subscriptions</a></li> -->

                            <li><a href="{{ route('admin.restaurant.list') }}">List Restaurant</a></li>
                            @endif
                            <li><a href="{{ route('admin.food_menu.list') }}">List Items</a></li>
                            @if($user->hasRole('food-admin'))
                            <li><a href="{{ route('admin.food_course.list') }}">List Course</a></li>
                            <li><a href="{{ route('admin.food_category.list') }}">List Category</a></li>
                            @endif
                    </ul>
                  </li>
                  @endif
                  @if($user->hasRole('restaurant-owner-single') || $user->hasRole('restaurant-owner-catering') )
                  <li>
                    <a href="{{ route('admin.order.invoices') }}">
                      <i class="fa fa-cog"></i>Download Invoices</a>
                    </a>
                  </li>
                  @endif
                  @if($user->hasRole('restaurant-owner-single|food-admin') || $user->hasRole('restaurant-owner-catering') )
                  <li>
                    <a href="{{ route('admin.batch.search') }}">
                      <i class="fa fa-search"></i>Batch Search</a>
                    </a>
                  </li>
                  @endif
                  @if($user->hasRole('restaurant-owner-catering') )
                  <li>
                    <a href="{{ route('admin.batch.worker') }}">
                      <i class="fa fa-sticky-note"></i>Worker List</a>
                    </a>
                  </li>
                  @endif
                  @if($user->hasRole('food-admin'))
                  <li><a><i class="fa fa-truck"></i> Trip <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <!-- <li><a href="{{ route('admin.trip.add') }}">Create Trip</a></li> -->
                        <li><a href="{{ route('admin.trip.list') }}">List Trips</a></li>
                    </ul>
                  </li>

                  @endif

                  @if($user->hasRole('food-admin'))
                  <li><a><i class="fa fa-gift"></i> Coupons <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.coupon.add') }}">Create Coupon</a></li>
                        <li><a href="{{ route('admin.coupon.list') }}">List Coupons</a></li>
                    </ul>
                  </li>
                  @endif

                  @if($user->can('view.settings-general'))
                  {{-- @if($user->hasRole('admin') || $user->hasRole('food-admin')) --}}
                  <li>
                    <a href="{{ route('admin.settings.show') }}">
                      <i class="fa fa-cog"></i>Settings </a>
                    </a>
                  </li>
                  @endif
                  @if($user->can('view.send-notification'))
                  <li>
                    <a href="{{ route('admin.notification.add') }}">
                      <i class="fa fa-bell"></i>Send Notification </a>
                    </a>
                  </li>
                  @endif
                  @if($user->can('view.activity-logs'))
                  <li>
                    <a href="{{ route('admin.activity.logs') }}">
                      <i class="fa fa-archive"></i>Activity Logs </a>
                    </a>
                  </li>
                  @endif
                  @if($user->can('create.spuul-add|view.spuul-list'))
                  <li><a><i class="fa fa-truck"></i> Spuul Plan<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('create.spuul-add'))
                        <li><a href="{{ route('admin.spuul.plan.add') }}">Add Plan</a></li>
                      @endif
                      @if($user->can('view.spuul-list'))
                        <li><a href="{{ route('admin.spuul.plan.list') }}">List Plan</a></li>
                      @endif
                        {{-- <li><a href="{{ route('admin.share.list') }}">Share Settings</a></li> --}}
                    </ul>
                  </li>
                  @endif
                  @if($user->can('view.settings-share') || $user->hasRole('food-admin'))
                  <li><a><i class="fa fa-slideshare"></i> Share Settings<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        @if($user->hasRole('food-admin'))
                        <li><a href="{{ route('admin.share.food') }}">Merchants</a></li>
                        @else
                        <li><a href="{{ route('admin.share.courses') }}">Courses</a></li>
                        <li><a href="{{ route('admin.share.flexm') }}">Flexm</a></li>
                        <li><a href="{{ route('admin.share.catering') }}">ISO Delight</a></li>
                        <li><a href="{{ route('admin.share.naanstap') }}">JK</a></li>
                        <li><a href="{{ route('admin.share.singx') }}">Singx</a></li>
                        <li><a href="{{ route('admin.share.spuul') }}">Spuul</a></li>
                        @endif
                    </ul>
                  </li>
                  @endif
                  {{-- @if($user->hasRole('spuul') || $user->hasRole('food-admin') || $user->hasRole('training')) --}}

                  @if($user->can('view.transaction-list') && !$user->hasRole('admin') && !$user->hasRole('food-admin'))
                  <!-- <li><a href="{{ route('admin.payout.view', ['merchant_id' => 2]) }}"><i class="fa fa-dollar"></i> Payout</a> -->
                  <!-- <li><a href="{{ route('admin.transactions.inapp') }}"><i class="fa fa-dollar"></i> Transactions</a> -->
                  @endif

                  @if($user->can('view.transaction-list-inapp') || $user->can('view.transaction-list-instore') || $user->can('view.transaction-list-remittance') || $user->can('view.payout-list') )
                  <li><a><i class="fa fa-dollar"></i> Transactions <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if( $user->can('view.transaction-list-inapp') )
                        @if($user->hasRole('food-admin'))
                          <li><a href="{{ route('admin.food.transactions') }}">List</a>
                        @else
                          @if($is_instore)
                            <li><a href="{{ route('admin.transactions.instore') }}">List In-Store</a></li>
                          @else
                            <li><a href="{{ route('admin.transactions.inapp') }}">List In-App</a></li>
                          @endif
                        @endif
                      @endif
                      @if( $user->can('view.transaction-list-instore') )
                        <li><a href="{{ route('admin.transactions.instore') }}">List In-Store</a></li>
                      @endif
                      @if( $user->can('view.transaction-list-remittance') )
                        <li><a href="{{ route('admin.transactions.remit') }}">List Remittance</a></li>
                      @endif
                      @if( $user->can('view.transaction-list-inapp') )
                      <li><a href="{{ route('admin.transactions.wallet') }}">List Wallet Transfer</a></li>
                      @endif
                      @if( $user->can('view.payout-list') )
                        <li><a href="{{ route('admin.payout.users') }}">Payout</a></li>
                      @endif
                      @if( $user->hasRole('food-admin') )
                        <li><a href="{{ route('admin.payout.wlc') }}">Payout from WLC</a></li>
                      @endif
                    </ul>
                  </li>
                  @endif

                  @if($user->can('view.merchant-list|create.merchant-add') )
                  <li><a><i class="fa fa-map-signs"></i>Physical Merchant<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      @if($user->can('view.merchant-list') )
                        <li><a href="{{ route('admin.merchant.list') }}">List Physical Merchant</a></li>
                      @endif
                      @if($user->can('create.merchant-add') )
                        <li><a href="{{ route('admin.merchant.add') }}">Add Physical Merchant</a></li>
                      @endif
                    </ul>
                  </li>
                  @endif

                  @if($user->can('view.report-menu') )
                  <li><a><i class="fa fa-list-alt"></i> Menu Statistics<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.menu.user') }}">By User</a></li>
                        <li><a href="{{ route('admin.menu.dormitory') }}">By Dormitory</a></li>
                    </ul>
                  </li>
                  @endif

                  @if($user->can('view.report-revenue') )
                  <li>
                    <a href="{{ route('admin.revenue.report') }}">
                      <i class="fa fa-dollar"></i>Revenue report </a>
                    </a>
                  </li>
                  @endif

                  @if($user->can('view.report-payment') )
                  <li>
                    <a href="{{ route('admin.payment.report') }}">
                      <i class="fa fa-dollar"></i>Payment report </a>
                    </a>
                  </li>
                  @endif

                  @if($user->can('view.report-revenue') )
                  <li>
                    <a href="{{ route('admin.redeem.report') }}">
                      <i class="fa fa-dollar"></i>Redeem report </a>
                    </a>
                  </li>
                  @endif

                  @if($user->can('view.transaction-list-singx') )
                  <li><a><i class="fa fa-map-signs"></i>Singx<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('admin.singx.list') }}">List Singx</a></li>
                        <li><a href="{{ route('admin.singx.remittance') }}">List Remittance</a></li>
                    </ul>
                  </li>
                  @endif
                  @if($user->hasRole('admin') )
                  <li>
                    <a href="{{ route('flexm.files') }}">
                      <i class="fa fa-dollar"></i>Flexm Files</a>
                    </a>
                  </li>
                  @endif
                  <!-- <li><a><i class="fa fa-clone"></i>Layouts <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="fixed_sidebar.html">Fixed Sidebar</a></li>
                      <li><a href="fixed_footer.html">Fixed Footer</a></li>
                    </ul>
                  </li> -->
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <!-- <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
              </a>
              <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('logout') }}" onclick="event.preventDefault();
                       document.getElementById('logout-form').submit();">

                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
              </a>
            </div> -->
            <!-- /menu footer buttons -->
          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav hidden-print">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
				      <div class="pull-right">
                <div id="google_translate_element"></div>
                </div>
              <ul class="nav navbar-nav navbar-right">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                      <?php
                          if(Auth::check() && Auth::user()->name != ''){
                              echo Auth::user()->name;
                          }else{
                              echo 'Admin';
                          }
                      ?>
                    <!--<img src="{{  static_file('assets/admin/images/img.jpg') }}" alt="">-->
                    <span class=" fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
                    <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>
              </ul>
            </nav>
          </div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          @include('errors.flash-message')
          @include('errors.error')
          @yield('content')
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <!-- <footer>
          <div class="pull-right">

          </div>
          <div class="clearfix"></div>
        </footer> -->
        <!-- /footer content -->
      </div>
    </div>

    <!-- Modal -->
<div id="userModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">User</h4>
      </div>
      <div class="modal-body">
          <div class="well profile">
              <!-- <div class="col-sm-12">
                  <div class="col-xs-12 col-sm-8">
                      <h2>Nicole Pearson</h2>
                      <p><strong>Email: </strong> Web Designer / UI. </p>
                      <p><strong>Dormitory: </strong> Read, out with friends, listen to music, draw and learn new things. </p>
                      <p><strong>Fin No: </strong> </p>
                  </div>
                  <div class="col-xs-12 col-sm-4 text-center">
                      <figure>
                          <img src="" alt="" class="img-circle img-responsive">
                      </figure>
                  </div>
              </div> -->
      	 </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div class="modal fade in" tabindex="-1" role="dialog" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-top: -10px;">×</button>
                <div>Are you sure about deleting this?</div>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="delete_button btn btn-default">Yes</a>
                <button type="button" data-dismiss="modal" class="btn btn-primary">Cancel</button>
            </div>
        </div>
    </div>
</div>
    @endif

    <!-- jQuery -->
    <script src="{{  static_file('assets/admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{  static_file('assets/admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{  static_file('assets/admin/vendors/fastclick/lib/fastclick.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-footable/3.1.6/footable.min.js"></script>
    <!-- NProgress -->
    <!-- <script src="{{  static_file('assets/admin/vendors/nprogress/nprogress.js') }}"></script> -->
    <!-- Chart.js -->
    <!-- <script src="{{  static_file('assets/admin/vendors/Chart.js/dist/Chart.min.js') }}"></script> -->
    <!-- gauge.js -->
    <script defer src="{{  static_file('assets/admin/vendors/gauge.js/dist/gauge.min.js') }}"></script>
    <!-- bootstrap-progressbar -->
    <script src="{{  static_file('assets/admin/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>
    <!-- iCheck -->
    <script src="{{  static_file('assets/admin/vendors/iCheck/icheck.min.js') }}"></script>
    <!-- Skycons -->
    <script src="{{  static_file('assets/admin/vendors/skycons/skycons.js') }}"></script>
    <!-- Flot -->
    <!-- <script src="{{  static_file('assets/admin/vendors/Flot/jquery.flot.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/Flot/jquery.flot.pie.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/Flot/jquery.flot.time.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/Flot/jquery.flot.stack.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/Flot/jquery.flot.resize.js') }}"></script>-->
    <!-- Flot plugins -->
    <!-- <script src="{{  static_file('assets/admin/vendors/flot.orderbars/js/jquery.flot.orderBars.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/flot-spline/js/jquery.flot.spline.min.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/flot.curvedlines/curvedLines.js') }}"></script> -->
    <!-- DateJS -->
    <script src="{{  static_file('assets/admin/vendors/DateJS/build/date.js') }}"></script>
    <!-- JQVMap -->
    <!-- <script src="{{  static_file('assets/admin/vendors/jqvmap/dist/jquery.vmap.js') }}"></script> -->
    <!-- <script src="{{  static_file('assets/admin/vendors/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script> -->
    <!-- <script src="{{  static_file('assets/admin/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js') }}"></script> -->
    <!-- bootstra{{  static_file('assets/admin/vendors/Flot/jquery.flot.time.js') }}picker -->
    <script src="{{  static_file('assets/admin/vendors/moment/min/moment.min.js') }}"></script>
    <script defer src="{{  static_file('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script defer src="{{  static_file('js/plugins/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script defer src="{{  static_file('js/plugins/bootstrap-fancyfile-master/js/bootstrap-fancyfile.min.js') }}"></script>

    <script src="{{  static_file('assets/admin/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/datatables.net-scroller/js/dataTables.scroller.min.js') }}"></script>
    <script src="{{  static_file('assets/admin/vendors/select2/dist/js/select2.min.js') }}"></script>

    @yield('scripts')
    <!-- Custom Theme Scripts -->
    <script src="{{  static_file('assets/admin/js/custom.js') }}"></script>
    <script>
    $(document).on('ready', function (event) {
      $('body').on('click', '.post-deletee', function (event) {
          event.preventDefault();
          var href = $(this).attr('href');
          var obj = $(this);
          $('.delete_button').attr('href', href);
          $('#deleteModal').modal('show');
      });

      if($('.foo_table').length){
          $('.foo_table').footable();
      }
        $('.fancy_upload').fancyfile({
            text  : '',
            // style : 'btn-info',
            placeholder : 'Browse…'
        });
        $(".number_only").keydown(function (event) {
            if (event.shiftKey == true) {
                event.preventDefault();
            }

            if ((event.keyCode >= 48 && event.keyCode <= 57) ||
                (event.keyCode >= 96 && event.keyCode <= 105) ||
                event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
                event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }

            if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                event.preventDefault();
            //if a decimal has been added, disable the "."-button

        });
    });



    $('#userModal').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var user_id = button.attr('user_id')

        $.ajax({
            data: {user_id:user_id},
            url: '{{ route('admin.user.get') }}',
            error: function(xhr){
                console.log("Error");
                console.log(xhr);
                $('.well.profile').html(xhr.statusText);
                // alert("An error occured: " + xhr.status + " " + xhr.statusText);
            },
            success: function(xhr){
                console.log("Success");
                var data = JSON.parse(xhr);
                console.log(data);
                if(data.status){
                    var html =   '<div class="userImg">'+
                      '<figure>'+
                      '<img src="{{public_url('/') }}/';
                      if(data.user.profile_pic != undefined){
                          html += data.user.profile_pic;
                      }
                      html += '" alt="" class="img-responsive">'+
                      '</figure>'+

                      '</div>'+

                      '<div class="userCon">'+
                    '<h2>'+data.user.name+'</h2>'+
                    '<p><strong>Email: </strong> '+data.user.email+' </p>'+
                    '<p><strong>Dormitory: </strong> ';

                    if(data.user.dormitory != undefined){
                        html += data.user.dormitory.name;
                    }
                    html += ' </p>'+
                    // '<p><strong>Fin No: </strong> ';
                    // if(data.user.profile != undefined){
                    //     html += data.user.profile.fin_no;
                    // }
                    // html += '</p>'+
                    '</div>';

                }else{
                    var html = 'Something went wrong';
                }
                $('.well.profile').html(html);
                // alert("An error occured: " + xhr.status + " " + xhr.statusText);
            }
        });
    });

    </script>

    <script>
    // Initialize Firebase
    var config = {
      apiKey: "AIzaSyD3F8J2jwnGY7zTmDdhgMu6ebJJ5CSAuHM",
      authDomain: "myma-1520248374001.firebaseapp.com",
      databaseURL: "https://myma-1520248374001.firebaseio.com",
      projectId: "myma-1520248374001",
      storageBucket: "myma-1520248374001.appspot.com",
      messagingSenderId: "185969502127"
    };
    firebase.initializeApp(config);
    //
    const messaging = firebase.messaging();
    messaging.usePublicVapidKey("BGpkX8Tf4yTLmUoCNoMF6tay_DkN9CPb8IIuPy2VSiOxTE4cKq7NDsjuLFosB0yNxTkqo0tIlzPerNSLPM3W7Zg");
    messaging.requestPermission().then(function() {
        console.log('Notification permission granted.');
        // TODO(developer): Retrieve an Instance ID token for use with FCM.
        return messaging.getToken();
    }).then(function(token) {
      $.ajax({
        url:'{{ route("ajax.add.token") }}',
        type: "post",
        data: {fcm_token : token,'_token': '{{ csrf_token() }}'},
        success: function(data){
          var res = JSON.parse(data);
          if(res.status){
            //alert(res.msg);
          }else{
            //alert(res.message);

            //window.location = '{{ route("food.customer.cart") }}';
          }
        },
        error: function(data){
          //alert("There was an issue while adding token.Try Again");
          console.log(data);
        }
      });
    })
    .catch(function(err) {
        console.log('Unable to get permission to notify.', err);
    });

      // Callback fired if Instance ID token is updated.
      messaging.onTokenRefresh(function() {
        messaging.getToken().then(function(refreshedToken) {
          console.log('Token refreshed.');
          $.ajax({
            url:'{{ route("ajax.add.token") }}',
            type: "post",
            data: {fcm_token : refreshedToken,'_token': '{{ csrf_token() }}'},
            success: function(data){
              var res = JSON.parse(data);
              if(res.status){
                //alert(res.msg);
              }else{
                //alert(res.message);

                //window.location = '{{ route("food.customer.cart") }}';
              }
            },
            error: function(data){
              //alert("There was an issue while adding token.Try Again");
              console.log(data);
            }
          });
        }).catch(function(err) {
          console.log('Unable to retrieve refreshed token ', err);
        });
      });
      messaging.onMessage(function(payload) {
        console.log('Message received. ', payload);

        let notification = new Notification(payload.notification.title, {
          icon: 'https://myhype.space/logo.png',
          body: payload.notification.body,
          click_action: payload.notification.click_action,
          tag: 'myma'
        });

        notification.onclick = function() {
          parent.focus();
          window.focus(); //just in case, older browsers
          this.close();
        };
        setTimeout(notification.close.bind(notification), 5000);

      });

    </script>
  </body>
</html>
