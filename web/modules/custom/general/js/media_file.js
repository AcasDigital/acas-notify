(function ($, Drupal) {
  Drupal.behaviors.mediaFileBehavior = {
    attach: function (context, settings) {
      $(context).find('.media-file .show-html').once('processed').each(function() {
        $(this).click(function() {
          var media_file = $(this).closest(".media-file");
          if (!$(media_file).find(".field--name-field-html-url").is(":visible")) {
            $(this).find(".text").text("Hide");
            $(this).find(".glyphicon").removeClass("glyphicon-plus");
            $(this).find(".glyphicon").addClass("glyphicon-minus");
            $(media_file).find(".field--name-field-html-url").slideDown();
            if (!$(media_file).find(".field--name-field-html-url iframe").hasClass("refreshed")) {
              $(media_file).find(".field--name-field-html-url iframe").addClass("refreshed");
              $(media_file).find(".field--name-field-html-url iframe").attr("src", $(media_file).find(".field--name-field-html-url iframe").attr("src"));
            }
          }else{
            $(this).find(".text").text("Show");
            $(this).find(".glyphicon").removeClass("glyphicon-minus");
            $(this).find(".glyphicon").addClass("glyphicon-plus");
            $(media_file).find(".field--name-field-html-url").slideUp();
          }
        });
      });
    }
  };
})(jQuery, Drupal);