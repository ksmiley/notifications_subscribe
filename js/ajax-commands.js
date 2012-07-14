// $Id: //sms/modules/morris/notifications_subscribe/6/v2011.1/js/ajax-commands.js#1 $
/**
 * @file
 *
 * Adds some AJAX commands needed by the follow screens.
 */
(function ($) {
  Drupal.CTools = Drupal.CTools || {};
  Drupal.CTools.AJAX = Drupal.CTools.AJAX || {};
  Drupal.CTools.AJAX.commands = Drupal.CTools.AJAX.commands || {};

  Drupal.CTools.AJAX.commands.removeClass = function(data) {
    $(data.selector).removeClass(data.classes);
  };

  Drupal.CTools.AJAX.commands.addClass = function(data) {
    $(data.selector).addClass(data.classes);
  };

})(jQuery);
