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
