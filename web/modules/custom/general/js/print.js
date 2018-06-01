var win;

Drupal.behaviors.print = {
  attach: function(context, settings) {
    jQuery(".print-download-email .print").click(function() {
      win = window.open(this.href);
      jQuery(win.document).ready(function() {
        setTimeout(doPrint, 1000);
      });
      return false;
    });
  }
};

function doPrint() {
  win.print();
  win.close();
}