<div class="row">
<div class="text-left col-md-5" style="padding-top:8px;">
    <?php
      if($items->currentPage() == 1)
          $start = 1;
      else{
          $start = ($items->currentPage()-1)*$items->perPage()+1;
      }

      if($items->currentPage() == $items->lastPage()){
          $last = $items->total();
      }else{
          $last = ($items->currentPage()) * $items->perPage();
      }

      echo "Showing ".$start." to ".$last." of ".$items->total()." entries";
    ?>
</div>
<div class="col-md-7 text-right">

    {{ $items->appends($paginate_data)->links('partials.test') }}
    @if($items->total())
    @endif
</div>
</div>
