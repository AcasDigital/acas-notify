var win;

Drupal.behaviors.views_exposed_form = {
  attach: function(context, settings) {
    var op = jQuery("#views-exposed-form-solr-search-content-page-1 .form-type-select");
    if (op) {
      jQuery("#views-exposed-form-solr-search-content-page-1 .form-type-select").remove();
      jQuery("#views-exposed-form-solr-search-content-page-1").append(op);
      jQuery("#views-exposed-form-solr-search-content-page-1 .form-type-select select").change(function(e) {
        var form = jQuery(this).closest('form');
        if (form) {
          jQuery(form).css({'cursor' : 'wait'});
          jQuery(form).find('input').css({'cursor' : 'wait'});
          jQuery(form).find('button').css({'cursor' : 'wait'});
          jQuery(form).find('select').css({'cursor' : 'wait'});
          jQuery('#block-exposedformsolr-search-contentpage-1-2').css({'cursor' : 'wait'});
          jQuery(form).submit();
        }
      });

    }
  }
};