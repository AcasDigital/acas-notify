Drupal.behaviors.acas = {
  attach: function(context, settings) {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      jQuery('body').addClass('ie' + parseInt(ua.substring(msie + 5, ua.indexOf(".", msie))));
    }
    document.body.style.display="block";
  }
};

document.getElementById("menu-primary__icon").addEventListener("click", function(e) {
  e.preventDefault();
  openNavigation();
});

function openNavigation() {
  var x = document.getElementById("menu-primary");
  if (x.className === "menu-primary") {
      x.className += " menu-primary--active";
  } else {
      x.className = "menu-primary";
  }
}

if (document.documentElement.clientWidth < 768) {
  var primaryListItems = document.getElementsByClassName("menu-primary__item");

  // window.isMobile = /iphone|ipod|ipad|android|blackberry|opera mini|opera mobi|skyfire|maemo|windows phone|palm|iemobile|symbian|symbianos|fennec/i.test(navigator.userAgent.toLowerCase());

  for(var i = 0; i < primaryListItems.length; i++) {
    var primaryLink = primaryListItems[i].querySelectorAll('.menu-primary__link')[0];
    primaryLink.addEventListener("click", function(e) {
      e.preventDefault();
      if(this.parentNode.classList.contains('menu-primary__item--active')) {
        this.parentNode.classList.remove('menu-primary__item--active')
      }
      else {
        this.parentNode.classList.add('menu-primary__item--active')
      }
    });
  }
}

// document.getElementById('edit-keys').addEventListener("click", function() {
//   document.getElementsByClassName('form--inline')[0].classList.add('active');
// });


jQuery( document ).ready( function( $ ) {
  $('#edit-keys').on("click focus", function() {
    $('.form--inline').addClass('active');
  });
  $('#edit-keys').on("focusout", function() {
    $('.form--inline').removeClass('active');
  });
	$( '.menu-primary' ).on( 'mouseenter focus', '.menu-primary__item > .menu-primary__link', function( e ) {
			var el = $( this );
      setTimeout( function() {
	       el.toggleClass( 'has-focus' );
      }, 100 );
			// Show sub-menu
			el.parents( '.menu-primary__item' ).attr( 'aria-expanded', 'true' );
		}).on( 'mouseleave blur', '.menu-primary__item > .menu-primary__link', function( e ) {
			var el = $( this );
	    el.toggleClass( 'has-focus' );
			// Only hide sub-menu after a short delay, so links get a chance to catch focus from tabbing
			setTimeout( function() {
				if ( el.siblings( '.menu-secondary' ).attr( 'data-has-focus' ) !== 'true' ) {
					el.parents( '.menu-primary__item' ).attr( 'aria-expanded', 'false' );
				}
			}, 100 );
		}).on( 'mouseenter focusin', '.menu-secondary', function( e ) {
			var el = $( this );
			el.attr( 'data-has-focus', 'true' );
		}).on( 'mouseleave focusout', '.menu-secondary', function( e ) {
			var el = $( this );
			setTimeout( function() {
				// Check if anything else has picked up focus (i.e. next link in sub-menu)
				if ( el.find( ':focus' ).length === 0 ) {
					el.attr( 'data-has-focus', 'false' );
					// Hide sub-menu on the way out if parent link doesn't have focus now
        	if ( el.siblings( '.menu-primary__link.has-focus' ).length === 0 ) {
						el.parents( '.menu-primary__item' ).attr( 'aria-expanded', 'false' );
          }
        }
			}, 100 );
		});
});
