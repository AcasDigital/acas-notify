Drupal.behaviors.acas = {
  attach: function(context, settings) {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      jQuery('body').addClass('ie' + parseInt(ua.substring(msie + 5, ua.indexOf(".", msie))));
    }
    document.body.style.display="block";
    jQuery('a').each(function() {
      if (jQuery(this).attr('href')) {
        if (jQuery(this).attr('href').indexOf('http') != -1) {
          jQuery(this).attr('target', '_blank');
          jQuery(this).addClass('link--external');
					jQuery('<span class="visuallyhidden">opens in new window</span>').insertBefore(this);
        }
      }
    });
  }
};

jQuery( document ).ready( function( $ ) {

  function openNavigation() {
    console.log('in here');
    var primaryMenu = $(".menu-primary");
    if (!primaryMenu.hasClass("menu-primary--active")) {
        primaryMenu.addClass("menu-primary--active");
    } else {
        primaryMenu.removeClass("menu-primary--active");
    }
  }

  $("#menu-primary__icon").click(function(e) {
    e.preventDefault();
    $(this).toggleClass('menu-primary__icon--active');
    openNavigation();
  });

  if (document.documentElement.clientWidth <= 768) {
    var primaryListItems = $(".menu-primary__item--has-dropdown");

    for(var i = 0; i < primaryListItems.length; i++) {
      var primaryLink = $(primaryListItems[i]).children('.menu-primary__link');
      console.log(primaryLink);
      primaryLink.click(function(e) {
        var linkParent = $(this).parent('.menu-primary__item');
        if(!linkParent.hasClass('menu-primary__item--active')) {
          e.preventDefault();
          linkParent.addClass('menu-primary__item--active')
        }
      });
    }
  }

  var secondaryMenus = $('.menu-secondary');

  for (var i = 0; i < secondaryMenus.length; i++) {
    var siblingLink = $(secondaryMenus[i]).siblings('.menu-primary__link');
    var secondaryMenuTitle = "<li class='menu-secondary__title'><h3>"+$(siblingLink).clone().html()+"</h3></li>";
    $(secondaryMenus[i]).prepend(secondaryMenuTitle);
  }

  /* Search form focus code */
  $('#edit-keys').on("click focus", function() {
    $('.form--inline').addClass('active');
  });
  $('#edit-keys').on("focusout", function() {
    $('.form--inline').removeClass('active');
  });

  /* Main Navigation Code */
	$( '.menu-primary__item' ).on( 'mouseenter focus', '> .menu-primary__link', function( e ) {
			var el = $( this );
      setTimeout( function() {
	       el.toggleClass( 'has-focus' );
      }, 100 );
			// Show sub-menu
			el.parents( '.menu-primary__item' ).attr( 'aria-expanded', 'true' );
		}).on( 'mouseleave blur', '> .menu-primary__link', function( e ) {
			var el = $( this );
	    el.toggleClass( 'has-focus' );
			// Only hide sub-menu after a short delay, so links get a chance to catch focus from tabbing
			setTimeout( function() {
				if ( el.siblings( '.menu-secondary' ).attr( 'data-has-focus' ) !== 'true' ) {
					el.parents( '.menu-primary__item' ).attr( 'aria-expanded', 'false' );
				}
			}, 350 );
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
			}, 350 );
		});
});
