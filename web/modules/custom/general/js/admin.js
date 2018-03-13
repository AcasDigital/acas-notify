(function ($, Drupal) {
  Drupal.behaviors.adminBehavior = {
    attach: function (context, settings) {
      $("#node-landing-page-edit-form #edit-field-taxonomy").change(function() {
        var title = $("option:selected", this).text().replace(/-/g, "");
        $("#node-landing-page-edit-form #edit-title-0-value").val(title);
      });
      $("#node-landing-page-form #edit-field-taxonomy").change(function() {
        var title = $("option:selected", this).text().replace(/-/g, "");
        $("#node-landing-page-form #edit-title-0-value").val(title);
      });
      
      $("#node-details-page-edit-form #edit-field-taxonomy").change(function() {
        var title = $("option:selected", this).text().replace(/-/g, "");
        $("#node-details-page-edit-form #edit-title-0-value").val(title);
      });
      $("#node-details-page-form #edit-field-taxonomy").change(function() {
        var title = $("option:selected", this).text().replace(/-/g, "");
        $("#node-details-page-form #edit-title-0-value").val(title);
      });
    }
  };
})(jQuery, Drupal);