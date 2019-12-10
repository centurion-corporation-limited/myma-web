<?php
/**
 * Created by PhpStorm.
 * User: NKT
 * Date: 14/12/2015
 * Time: 11:31
 */
?>

@if (Session::has('flash_message'))
<div class="clearfix"></div>
    <div class="alert alert-{!! Session::get('flash_level') !!}">
        {!! Session::get('flash_message') !!}
    </div>
@endif
