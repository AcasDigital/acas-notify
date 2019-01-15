/**
 * @file
 */

(function ($, Drupal, drupalSettings) {
/**
* @namespace
*/
Drupal.behaviors.exitpopupAccessData = {
attach: function (context, settings) {
    var widthDrupal, heightDrupal, htmlDrupal, cookieExpDrupal, delayDrupal,cssDrupal;
    widthDrupal = drupalSettings.drupal_epu_width;
    heightDrupal = drupalSettings.drupal_epu_height;
    htmlDrupal = drupalSettings.drupal_epu_html.replace(/[\r\n]/g, '');
    cssDrupal = drupalSettings.drupal_epu_css.replace(/[\r\n]/g, '');
    cookieExpDrupal = drupalSettings.drupal_epu_cookie_exp;
    delayDrupal = drupalSettings.drupal_epu_delay;

    bioEp.init({
        html: htmlDrupal,
        css: cssDrupal,
        width: widthDrupal,
        height: heightDrupal,
        delay: delayDrupal,
        cookieExp: cookieExpDrupal
    });
}
};
})(jQuery, Drupal, drupalSettings);
