<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Validator;
use JWTAuth, DB;
use App\Models\Pages;
use App\Models\Category;

use Carbon\Carbon;

class PagesController extends Controller
{
    public function listout(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));
        try{

          $lang = $request->language?($request->language):'english';
          $token = @$request->token;
          if($lang == 'chinese'){
              $lang = 'mandarin';
          }
          $pages = Pages::whereHas('lang_content', function($q) use($lang){
              $q->where('language', $lang);
          })->select('id', 'title')->get();

          $is_contact = true;

          foreach($pages as $pp){
              $pp->title = $pp->content($lang)->first()->title;
              // $pp->content = $pp->content($lang)->first()->content;
              $pp->url = route('app.page', [$pp->id]);
              if($pp->id == 4){
                  $is_contact = false;
                  if($token != ''){
                      $pp->url = route('contact', ['token' => $token]);
                  }else{
                      $pp->url = route('contact');
                  }
              }
          }

          if($is_contact){
              if($token != ''){
                  $url = route('contact', ['token' => $token,'language' => $lang]);
              }else{
                  $url = route('contact',['language' => $lang]);
              }
              $c_id = $pages->last()?($pages->last()->id+1):1;
              $dd[$c_id]['id'] = $c_id;
              $dd[$c_id]['title'] = 'Contact Us';
              $dd[$c_id]['url'] = $url;
              $pages = $pages->toArray();
              $pages = array_merge($pages,$dd);
          }
          $list['tnc'] = url('terms');
          $list['privacy'] = url('privacy');
          $list['contact'] = url('contact');
          $list['faq'] = url('faq');


          $mwc['who_we_are'] = getOption('who_we_are');//'http://mwc.org.sg/wps/portal/mwc/home/Aboutus/whoweare/!ut/p/a1/04_Sj9CPykssy0xPLMnMz0vMAfGjzOIDfPw9Xd08jAwsLMPMDTxNgwy8fQNdnYJ9DYAKIoEKDHAARwNC-sP1o8BK8JhQkBthkO6oqAgAoizWYg!!/dl5/d5/L2dJQSEvUUt3QS80SmlFL1o2XzgzOEYxUzQ4Q1BCRjkwQUtSQ1RNUUEyNDk1/';
          $mwc['what_we_do'] = getOption('what_we_do');//'http://mwc.org.sg/wps/portal/mwc/home/Aboutus/whatwedo/!ut/p/a1/04_Sj9CPykssy0xPLMnMz0vMAfGjzOIDfPw9Xd08jAwsLMPMDTxNgwy8fQNdnYJ9DYAKIoEKDHAARwNC-sP1o8BK8JhQkBthkO6oqAgAoizWYg!!/dl5/d5/L2dJQSEvUUt3QS80SmlFL1o2XzgzOEYxUzQ4Q1BCRjkwQUtSQ1RNUUEyNFAy/';
          $mwc['help'] = getOption('mwc_help');//'http://mwc.org.sg/wps/portal/mwc/home/services/helpkiosk/!ut/p/a1/04_Sj9CPykssy0xPLMnMz0vMAfGjzOItjC3cDINNLJwDnNwsDRy9g5xDfAMdjUwsTYEKIoEKDHAARwOw_gAff09XNw8jAwvLMHMDT9MgA2_fQFenYF8DqH48CgjYH64fBVaCzwV43RBiSkAB0A0FuaERBpmeigD0LJjM/dl5/d5/L2dJQSEvUUt3QS80SmlFL1o2X0o4RUdIQ1MwSjg2VTMwQVA0NVBIVFEzT0cz/';
          $mwc['kiosks'] = getOption('mwc_kiosks');//'http://mwc.org.sg/wps/portal/mwc/home/services/helpkiosk/!ut/p/a1/04_Sj9CPykssy0xPLMnMz0vMAfGjzOItjC3cDINNLJwDnNwsDRy9g5xDfAMdjUwsTYEKIoEKDHAARwOw_gAff09XNw8jAwvLMHMDT9MgA2_fQFenYF8DqH48CgjYH64fBVaCzwV43RBiSkAB0A0FuaERBpmeigD0LJjM/dl5/d5/L2dJQSEvUUt3QS80SmlFL1o2X0o4RUdIQ1MwSjg2VTMwQVA0NVBIVFEzT0cz/';
          $mwc['legal_clinic'] = getOption('mwc_legal_clinic');//'http://mwc.org.sg/wps/portal/mwc/home/services/freelegalclinic/!ut/p/a1/04_Sj9CPykssy0xPLMnMz0vMAfGjzOItjC3cDINNLJwDnNwsDRy9g5xDfAMdjUwsTYEKIoEKDHAARwOw_gAff09XNw8jAwvLMHMDT9MgA2_fQFenYF8DqH48CgjYH64fBVaCzwV43RBiSkAB0A0FuaERBpmeigD0LJjM/dl5/d5/L2dJQSEvUUt3QS80SmlFL1o2XzgzOEYxUzQ4Q0hVVTAwQTRETjhJVEpMQjI1/';
          $mwc['fair_network'] = getOption('fair_network');//url('are');
          $mwc['contact'] = getOption('mwc_contact');//'http://mwc.org.sg/wps/portal/mwc/home/contactus/!ut/p/a1/04_Sj9CPykssy0xPLMnMz0vMAfGjzOItjC3cDINNLJwDnNwsDRy9g5xDfAMdjUwsTYEKIoEKDHAARwOw_gAff09XNw8jAwvLMHMDT9MgA2_fQFenYF8DqH48CgjYH64fBVaCzwV43RBiSkAB0A0FuaERBpmeigD0LJjM/dl5/d5/L2dJQSEvUUt3QS80SmlFL1o2XzgzOEYxUzQ4Q0hVVTAwQTRETjhJVEpMSFYx/';

          $flexm['tnc'] = url('flexm/terms');
          $flexm['manual'] = url('faq');
          $flexm['user_guide'] = url('flexm/guide');
          $flexm['support'] = url('flexm/support');
          $flexm['remittance_faq'] = url('flexm/faq');
          $flexm['remittance_how_to'] = url('flexm/how');//getOption('flexm_howto_content');//url('flexm/how');
          //$flexm['remittance_terms'] = url('remittance/terms');

          if($list){
            return response()->json(['status' => 'success', 'pages' => $pages, 'data' => $list, 'mwc' => $mwc, 'flexm' => $flexm, 'message' => 'DATA_LIST.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Their was a problem while fetching data. Try Later.', 'message' => 'ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }

    protected function view_validator(array $data)
    {
      return Validator::make($data, [
            'page_id'       => 'required',
        ]);
    }

    public function view(Request $request)
    {
        // $user = JWTAuth::toUser($request->input('token'));
        $validator = $this->view_validator($request->all());

        if ($validator->fails()) {
          $errors = $validator->errors();
          $message = [];
          foreach($errors->messages() as $key => $error){
              $message[$key] = $error[0];
          }
          return response()->json(['status' => 'error', 'data' => $message, 'message' => 'VALIDATION_ERROR'], 401);
        }

        try{

          $page_id = $request->page_id;
          $page = Pages::where('id', $page_id)->first();

          if($page){
            return response()->json(['status' => 'success', 'data' => $page, 'message' => 'SUCCESS.'], 200);
          }else{
            return response()->json(['status' => 'error', 'data' => 'Page does not exist.', 'message' => 'ERROR'], 404);
          }

        }catch(Exception $e){
          return response()->json(['status' => 'error', 'data' => $e->getMessage(), 'message'=> 'EXCEPTION_OCCURED'], $e->getStatusCode());
        }
    }
}
