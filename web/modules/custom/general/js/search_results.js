Drupal.behaviors.search_results = {
  attach: function(context, settings) {
    var keys = jQuery('#edit-keys').val();
    var reg = new RegExp(keys, "ig");
    jQuery('.view-solr-search-content .view-content .field--name-field-summary').each(function() {
      jQuery(this).html(jQuery(this).html().replace(reg, '<strong>' + keys + '</strong>'));
    });
    /*
    jQuery('.view-solr-search-content .view-content .breadcrumb--search a').each(function() {
      jQuery(this).html(jQuery(this).html().replace(reg, '<strong>' + keys.charAt(0).toUpperCase() + keys.slice(1) + '</strong>'));
    });
    */
  }
};