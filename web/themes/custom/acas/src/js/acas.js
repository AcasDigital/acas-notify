Drupal.behaviors.acas = {
  attach: function(context, settings) {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      jQuery('body').addClass('ie' + parseInt(ua.substring(msie + 5, ua.indexOf(".", msie))));
    }
    setTimeout(searchFocus, 200);
    document.body.style.display="block";
    function searchFocus() {
      document.getElementById("edit-keys").focus();
    }
  }
};


var dropdownButtons = (function() {
  // dom elements
  var menuTimer;
  var primaryWrappers = document.getElementsByClassName('menu-primary__item');
  var primaryLinks = 'menu-primary__link';
  var secondaryWrappers = document.getElementsByClassName('menu-secondary__item');
  var secondaryLinks = 'menu-secondary__link';

  var activeDropdowns = [];

  addEventListeners(primaryWrappers, primaryLinks);

  function addEventListeners(wrappers, linkClass) {
    // Add click listeners to all the dropdown buttons
    for (i = 0; i < wrappers.length; i++) {
      var listElement = wrappers[i];
      listElement.addEventListener("mouseenter", function() {
        showDropdown(this);
      });
    }
    for (c = 0; c < wrappers.length; c++) {
      var listElement = wrappers[c];
      listElement.addEventListener("mouseleave", function() {
        hideDropdown(this);
      });
    }
  }

  // Hide all other dropdowns
  function hideDropdown(activeItem) {
    menuTimer = setTimeout(function() {
      activeItem.classList.remove('active');
    }, 750);
  }

  // Toggle the dropdown
  function showDropdown(activeItem) {
    clearTimeout(menuTimer);
    activeItem.classList.add('active');
  }

})();
