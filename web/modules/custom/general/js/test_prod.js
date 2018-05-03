Drupal.behaviors.test_prod = {
  attach: function(context, settings) {
    jQuery.ajax({
      url: "/sync-prod-data",
      type: "GET",
      dataType: "json",
      cache: false,
      timeout: 60000,
      error: function(XMLHttpRequest, textStatus, errorThrown){
        
      },
      success: function(data){
        var prod = data.prod
        var d = new Date();
        for( var i =0; i < data.nodes.length; i++) {
          var node = data.nodes[i];
          jQuery.ajax({
            url: data.prod + node.url + "?" + d.getTime(),
            type: "GET",
            dataType: "html",
            cache: false,
            node: node,
            timeout: 60000,
            error: function(XMLHttpRequest, textStatus, errorThrown){
              debugger;
            },
            success: function(data){
              debugger;
            }
          });
        }
      }
    });
  }
};