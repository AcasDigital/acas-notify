Drupal.behaviors.print = {
  attach: function(context, settings) {
    jQuery(".print-download-email .print").click(function() {
      var win = window.open(this.href);
      win.print();
      win.close();
      return false;
    });
  }
};