Drupal.behaviors.acas = {
  attach: function(context, settings) {
    setTimeout(searchFocus, 200);
    document.body.style.display="block";
    function searchFocus() {
      document.getElementById("edit-keys").focus();
    }
  }
};