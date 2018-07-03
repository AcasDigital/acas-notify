Drupal.behaviors.content_moderation_form = {
  attach: function(context, settings) {
    jQuery(".content-moderation-entity-moderation-form #close").click(function() {
      jQuery(".content-moderation-entity-moderation-form").hide();
    });
  }
};