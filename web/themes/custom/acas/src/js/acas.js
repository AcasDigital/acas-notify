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

document.getElementById("menu-primary__icon").addEventListener("click", openNavigation);

function openNavigation() {
    var x = document.getElementById("menu-primary");
    if (x.className === "menu-primary") {
        x.className += " menu-primary--active";
    } else {
        x.className = "menu-primary";
    }
}
