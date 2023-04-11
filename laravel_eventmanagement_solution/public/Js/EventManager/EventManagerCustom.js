$(function () {
    
    $(".datepicker").datepicker({
        
        dateFormat: 'dd-mm-yy',
        minDate: 0
    });
});



setTimeout(function () {
    $('#message').fadeOut('slow');
}, 5000); // 5 seconds

ClassicEditor
.create(document.querySelector('.content'))
.catch(error => {
    console.error(error);
});