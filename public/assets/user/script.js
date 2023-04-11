
  var swiper = new Swiper(".mySwiper", {
      spaceBetween: 20,
      slidesPerView: 1,
      loop: true,
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },
      breakpoints: {
        640: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 20,
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 30,
        }
      }
    });
$(document).ready(function() {
   $("#owl-example").owlCarousel(
    {
      loop:true,
      margin: 10,
      nav:false,
      dots:false,
      autoplay: true,
      responsive:{
          0:{
              items:1
          },
          600:{
              items:2
          },
          1000:{
              items:4
          }
      }
      }
   );
  //  $('.buy-carousel.owl-carousel').owlCarousel({
  //   loop: true,
  //   margin: 10,
  //   nav: false,
  //   dots: false,
  //   autoplay: true,
  //   responsive: {
  //     0: {
  //       items: 1
  //     },
  //     600: {
  //       items: 2
  //     },
  //     1000: {
  //       items: 4
  //     }
  //   }
  // });
  $('.listing-detail span').tooltip('hide');
        $('.carousel').carousel({
            interval: 3000
        }); 
        $('.carousel').carousel('cycle');
 }); 




  $(function() {
      
        var Page = (function() {

        	
          var $nav = $( '#nav-dots > span' ),
            slitslider = $( '#slider' ).slitslider( {
              onBeforeChange : function( slide, pos ) {

                $nav.removeClass( 'nav-dot-current' );
                $nav.eq( pos ).addClass( 'nav-dot-current' );

              }
            } ),

            init = function() {

              initEvents();
              
            },
            initEvents = function() {

              $nav.each( function( i ) {
              
                $( this ).on( 'click', function( event ) {
                  
                  var $dot = $( this );
                  
                  if( !slitslider.isActive() ) {

                    $nav.removeClass( 'nav-dot-current' );
                    $dot.addClass( 'nav-dot-current' );
                  
                  }
                  
                  slitslider.jump( i + 1 );
                  return false;
                
                } );
                
              } );

            };

            return { init : init };

        })();

        Page.init();

        /**
         * Notes: 
         * 
         * example how to add items:
         */

        /*
        
        var $items  = $('<div class="sl-slide sl-slide-color-2" data-orientation="horizontal" data-slice1-rotation="-5" data-slice2-rotation="10" data-slice1-scale="2" data-slice2-scale="1"><div class="sl-slide-inner bg-1"><div class="sl-deco" data-icon="t"></div><h2>some text</h2><blockquote><p>bla bla</p><cite>Margi Clarke</cite></blockquote></div></div>');
        
        // call the plugin's add method
        ss.add($items);

        */
      
      });