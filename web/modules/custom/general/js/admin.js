(function ($, Drupal) {
  Drupal.behaviors.adminBehavior = {
    attach: function (context, settings) {
      $(".view-ec-notifications .view-filters .js-form-type-textfield input").attr('type', 'date');
    }
  };
})(jQuery, Drupal);