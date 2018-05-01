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

// 
// var timeoutHandlers = [];
// var timeoutCounter = 1;
//
// var primaryLinks = document.getElementsByClassName('menu-primary__item');
//
// for (i = 0; i < primaryLinks.length; i++) {
//   primaryLinks[i].addEventListener('mouseenter, focusin', function() {
//     openSubmenu(this, i);
//   });
// }
//
// for (c = 0; c < primaryLinks.length; c++) {
//   primaryLinks[c].addEventListener('mouseleave', function() {
//     closeSubmenu(this, c);
//   });
// }
//
// // Hide all other dropdowns
// function closeSubmenu(e, index) {
//   console.log(index);
//   timeoutHandlers[timeoutCounter] = setTimeout(function() {
//     e.classList.remove('active');
//   }, 500);
// }
//
// // Toggle the dropdown
// function openSubmenu(e, index) {
//   console.log(index);
//   clearTimeout(timeoutHandlers[timeoutCounter]);
//   e.classList.add('active');
// }
