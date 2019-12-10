
var Notify = function() {
    var init = function() {
        if (!("Notification" in window)) {
            alert("This browser does not support desktop notification")
        } else if (Notification.permission === "granted") {} else if (Notification.permission !== 'denied') {
            Notification.requestPermission(function(permission) {})
        }
    };
    var show = function(title, message, icon, url) {
        var BA = new Notification(title, {
            icon: icon ? icon : basePath + '/public/notify-icon.png',
            body: message,
            tag: "nkt"
        });
        BA.addEventListener("click", function() {
            if (url) {
                window.open(url, '_blank');
                window.focus()
            } else {
                window.focus()
            }
            BA.close()
        })
    };
    return {
        init: init,
        show: show
    }
}();

jQuery.fn.preventDoubleSubmission = function() {
  $(this).on('submit',function(e){
      var $form = $(this);

      if ($form.data('submitted') === true) {
        // Previously submitted - don't submit again
        e.preventDefault();
      } else {
        // Mark it so that the next submit can be ignored
        $form.data('submitted', true);
      }
    });

    // Keep chainability
    return this;
};

$('.tags').tagsInput({
   width: 'auto',
   height: '50px'
  //tagClass: 'label label-info'
});

$('.review-tags').tagsInput({
   width: 'auto',
   height: '50px',
   //interactive: true
  //tagClass: 'label label-info'
});

$("input[name=photo]").change(function(){
    readURL(this, $(".campaign_photo"));
});


$("#start_date").datepicker({
    todayBtn:  1,
    autoclose: true,
    container: '#start-container',
    startDate: '1',
}).on('changeDate', function (selected) {
  if(selected.date != undefined)
    var minDate = new Date(selected.date.valueOf());
  else
    var minDate = new Date(this.value.valueOf());
    $('#end_date').datepicker('setStartDate', minDate);
    minDate.setDate(minDate.getDate() - 3);

    $('#close_date').datepicker('setEndDate', minDate);
});

$("#close_date").datepicker({
  todayBtn:  1,
  autoclose: true,
  container: '#close-container',
  startDate: '1',
}).on('changeDate', function (selected) {
  if(selected.date != undefined)
    var minDate = new Date(selected.date.valueOf());
  else
    var minDate = new Date(this.value.valueOf());

    //console.log(minDate);
    minDate.setDate(minDate.getDate() + 3);
    //$('#start_date').datepicker('setDate', minDate);
    // $('#end_date').datepicker('setDate', minDate);
    $('#close_date').datepicker('setEndDate', minDate);
    $('#end_date').datepicker('setStartDate', minDate);
    $('#start_date').datepicker('setStartDate', minDate);
});

$("#end_date").datepicker({
  todayBtn:  1,
  autoclose: true,
  container: '#end-container',
  startDate: '1',
}).on('changeDate', function (selected) {
  if(selected.date != undefined)
    var minDate = new Date(selected.date.valueOf());
  else
    var minDate = new Date(this.value.valueOf());
    $('#start_date').datepicker('setEndDate', minDate);

    minDate.setDate(minDate.getDate() - 3);

    $('#close_date').datepicker('setEndDate', minDate);
});

$('.time_picker').timepicker({
  minuteStep:5
});

if($('input[name=campaign_type_renum]').is(':checked')){
  $('.campaign_type_renum').removeClass('hide');
  $('input[name=remuneration]').attr('required' , 'true');
  $('input[name=no_of_influencer]').attr('required' , 'true');
}

// Display image when upload, used for change event
function readURL(input, imgElement) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            imgElement.attr('src', e.target.result);
        }
        imgElement.removeClass('hide');
        reader.readAsDataURL(input.files[0]);
    }
}
