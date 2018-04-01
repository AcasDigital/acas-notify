(function ($, Drupal) {
  Drupal.behaviors.anythingWrongBehavior = {
    attach: function (context, settings) {
      $("#anything-wrong-link").click(function() {
        $("#anything-wrong-link").hide();
        $("#anything-wrong-close").show();
        $(".webform-submission-form").slideDown();
        setTimeout(anythingWrongTextareaFocus,500);
      });
      $("#anything-wrong-close").click(function() {
        $("#anything-wrong-link").show();
        $("#anything-wrong-close").hide();
        $(".webform-submission-form").slideUp();
      });
      $(".form-item-page-url input").val(location.origin + location.pathname);
      var options = {
        beforeSubmit:  showAnythingWrongRequest,  // pre-submit callback 
        success:       showAnythingWrongResponse  // post-submit callback 
      };
      $('.webform-submission-form').ajaxForm(options);
      
      $("#feedback_wrapper #useful_wrapper a").click(function() {
        jQuery.ajax({
          url: "/feedback/" + jQuery(this).attr('nid') + "/" + jQuery(this).text() ,
          type: "GET",
          dataType: "html",
          cache: false,
          timeout: 60000,
          error: function(XMLHttpRequest, textStatus, errorThrown){
            
          },
          success: function(data){
            $("#feedback_wrapper #useful_wrapper").html("Thank you for your feedback");
          }
        });
        return false;
      });
      function showAnythingWrongRequest(formData, jqForm, options) {
        $("#anything-wrong-wrapper").html("<span class='thsnk-you'>Thank you for your message</span>");
        $(".webform-submission-form").slideUp();
      }
      function showAnythingWrongResponse(responseText, statusText, xhr, $form)  {
        
      }
      function anythingWrongTextareaFocus() {
        $(".webform-submission-form .form-type-textarea .form-textarea").focus();
      }
    }
  };
})(jQuery, Drupal);