Drupal.behaviors.acas = {
  attach: function(context, settings) {
    setTimeout(searchFocus, 200);
    document.body.style.display="block";
    
    jQuery(context).find('.node_view .print .btn').once('processed').each(function() {
      jQuery(this).html(jQuery(this).html() + '<span class="glyphicon glyphicon-print"></span>');
    });
    
    jQuery(context).find('.print__wrapper--pdf .print__link--pdf').once('processed').each(function() {
      jQuery(this).addClass('btn btn-primary');
      jQuery(this).html(jQuery(this).html() + '<span class="glyphicon glyphicon-download-alt"></span>');
    });

    function searchFocus() {
      document.getElementById("edit-search-api-fulltext").focus();
    }
  }
};