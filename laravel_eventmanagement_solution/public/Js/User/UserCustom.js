setTimeout(function () {
    $('#message').fadeOut('slow');
}, 5000); // 5 seconds

var swiper = new Swiper(".mySwiper", {
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    autoplay: {
        delay: 5000,
      },
});