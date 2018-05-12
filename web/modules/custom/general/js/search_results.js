Drupal.behaviors.search_results = {
  attach: function(context, settings) {
    var keys = jQuery('#edit-keys').val();
    var reg = new RegExp(keys, "ig");
    jQuery('.view-solr-search-content .view-content').html(jQuery('.view-solr-search-content .view-content').html().replace(reg, '<strong>' + keys + '</strong>'));
  }
};