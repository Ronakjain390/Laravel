setTimeout(function () {
  $('#message').fadeOut('slow');
}, 5000); // 5 seconds

$(function () {
  $(".datepicker").datepicker({

    dateFormat: 'dd-mm-yy',
    minDate:0
 });
});


$(function () {
  $(".timepicker").timepicker();
});

