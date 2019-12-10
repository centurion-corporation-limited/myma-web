@extends('layouts.admin')

@section('styles')
<style>
.fa-language{cursor: pointer;}
.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f1f1f1;
    min-width: 160px;
    overflow: auto;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: black;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown a:hover {background-color: #ddd}
.main_lang_drop{right: 15px;}
.show {display:inherit !important;}
</style>
@endsection
@section('content')

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Forum Details</h2>
            <div class="pull-right">
                <span class="dropdown-parent">
                    <i class="fa fa-language"></i>
                    <span id="myDropdown" class="dropdown-content myLanguage main_lang_drop">
                        <a href="javascript:;" class="lang_click" data-lang="bn">Bengali</a>
                        <a href="javascript:;" class="lang_click" data-lang="zh">Chinese</a>
                        <a href="javascript:;" class="lang_click" data-lang="en">English</a>
                        <a href="javascript:;" class="lang_click" data-lang="ta">Tamil</a>
                        <a href="javascript:;" class="lang_click" data-lang="th">Thai</a>
                    </span>
                </span>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            <form id="demo-form2" action="{{ route('admin.forum.reply', encrypt($item->id)) }}" method="POST" enctype="multipart/form-data"  data-parsley-validate class="form-horizontal form-label-left colord-form">
              {{ csrf_field() }}
              <div class="form-group">
                <!-- <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name">Comment</label> -->
                <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-ques">
               	   <h3>{{ $item->title }}</h3>
                   <p><span class="converted_text">{{ $item->content }}</span>
                       <!-- <span class="dropdown-parent">
                           <i class="fa fa-language"></i>
                           <span id="myDropdown" class="dropdown-content myLanguage">
                               <a href="javascript:;" class="lang_click" data-lang="bn">Bengali</a>
                               <a href="javascript:;" class="lang_click" data-lang="zh">Chinese</a>
                               <a href="javascript:;" class="lang_click" data-lang="en">English</a>
                               <a href="javascript:;" class="lang_click" data-lang="ta">Tamil</a>
                               <a href="javascript:;" class="lang_click" data-lang="th">Thai</a>
                           </span>
                       </span> -->
                   </p>


                  <p style="font-weight:300;">{{ $item->user->name }}
                  <span>{{ $item->created_at->format('d/m/Y h:i:A') }}</span></p>
                  </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12 pull-right">
                <div class="comments-row">
                    <h3>Comments</h3>
                    </div>
                    @foreach($item->comments as $comment)
                    <div class="from-view">
                      <p>
                          <span class="converted_text">{{ $comment->comment }}</span>
                          <!-- <span class="dropdown-parent">
                              <i class="fa fa-language"></i>
                              <span class="dropdown-content myLanguage">
                                  <a href="javascript:;" class="lang_click" data-lang="bn">Bengali</a>
                                  <a href="javascript:;" class="lang_click" data-lang="zh">Chinese</a>
                                  <a href="javascript:;" class="lang_click" data-lang="en">English</a>
                                  <a href="javascript:;" class="lang_click" data-lang="ta">Tamil</a>
                                  <a href="javascript:;" class="lang_click" data-lang="th">Thai</a>
                              </span>
                          </span> -->

                        <a href="{{ route('admin.comments.delete', ['id' => $comment->id, '_token' => csrf_token()]) }}" data-message="Are you sure about deleting this?" class="post-delete"><i class="fa fa-2x fa-trash-o"></i></a>
                      </p>

                      <p style="text-align:right;"> {{ $comment->user->name }}
                      <span>{{ $comment->created_at->format('d/m/Y h:i:A') }}</span></p>
                      </div>
                    @endforeach
                    <textarea placeholder="Comment" id="comment" name="comment" class="form-control col-md-7 col-xs-12">{{ old('name') }}</textarea>
                </div>

              </div>

              <div class="ln_solid"></div>
              <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <!-- <button class="btn btn-primary" type="button">Cancel</button> -->
                  <!-- <button class="btn btn-primary" type="reset">Reset</button> -->
                  <button type="submit" class="btn btn-success">Reply</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<script>
$(document).on('ready',function(){
  $(document).on('click', '.fa-language', function(){
     $('.dropdown-content').removeClass('show');
     $(this).next().toggleClass('show');
  });

  $(document).on('click', '.lang_click',function (event) {
      var lang = $(this).data('lang');
      var obj = $(this).closest('.dropdown-parent').prev();
      var text = obj.text();
      var txt = [];
      $('.converted_text').each(function(i,v){
          txt.push($(this).text());
      });
      console.log(txt);
      $.ajax({
          data: {language:lang, text:txt},
          url: '{{ route('ajax.language.convert') }}',
          error: function(xhr){
              console.log("Error");
              console.log(xhr);
              $('.dropdown-content').removeClass('show');
              // alert("An error occured: " + xhr.status + " " + xhr.statusText);
          },
          success: function(xhr){
              console.log("Success");
              var data = JSON.parse(xhr);
              console.log(data);
              $('.dropdown-content').removeClass('show');
              obj.text(data.text);
              // if(data.status){
              // }else{
              //     var html = 'Something went wrong';
              // }
              // $('.well.profile').html(html);
              // alert("An error occured: " + xhr.status + " " + xhr.statusText);
          }
      });
  });

});

</script>
@endsection
