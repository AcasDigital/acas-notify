var win;

Drupal.behaviors.views_exposed_form = {
  attach: function(context, settings) {
    
  }
};

function viewsExposedSubmitForm(form) {
  var keys = jQuery(form).find(".form-text");
  if (jQuery(keys).val().length < 3) {
    alert("Search must be at least 3 characters");
    jQuery(keys).focus();
    return false;
  }
  return true;
}