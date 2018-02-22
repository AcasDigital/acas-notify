(function ($, Drupal) {
  Drupal.behaviors.anythingWrongBehavior = {
    attach: function (context, settings) {
      $("#anything-wrong-link").click(function() {
        $("#anything-wrong-link").hide();
        $("#anything-wrong-close").show();
        $(".webform-submission-form").slideDown();
        $(".form-textarea").focus();
      });
      $("#anything-wrong-close").click(function() {
        $("#anything-wrong-link").show();
        $("#anything-wrong-close").hide();
        $(".webform-submission-form").slideUp();
      });
      var options = {
        beforeSubmit:  showAnythingWrongRequest,  // pre-submit callback 
        success:       showAnythingWrongResponse  // post-submit callback 
      };
      $('.webform-submission-form').ajaxForm(options);
      function showAnythingWrongRequest(formData, jqForm, options) {
        $("#anything-wrong-wrapper").html("<span class='thsnk-you'>Thank you for your message</span>");
        $(".webform-submission-form").slideUp();
      }
      function showAnythingWrongResponse(responseText, statusText, xhr, $form)  {
        
      }
    }
  };
})(jQuery, Drupal);