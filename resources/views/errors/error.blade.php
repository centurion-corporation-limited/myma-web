@if (isset($errors) && count($errors) > 0)
<div class="clearfix"></div>
    <?php
        $errs = array_unique($errors->all(), SORT_REGULAR);
    ?>
    <div class="alert alert-danger">
        <ul>
            @foreach ($errs as $error)
                <li>{!! $error !!}</li>
            @endforeach
        </ul>
    </div>
@endif
