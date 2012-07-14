// $Id: //sms/modules/morris/notifications_subscribe/6/v2011.1/js/modal-override.js#1 $
/**
 * @file
 * Backport of functions from CTools 1.8 so that the modal display is customizable.
 */
(function ($) {
  /**
   * Display the modal
   *
   * This is a backport of the show() method from CTools 1.8, which makes the modal 
   * much more adjustable. The calling functions that tell show() which settings to
   * use have not been backported, so this was hacked to either use the follow button
   * settings, or none at all.
   */
  Drupal.CTools.Modal.show = function(choice) {
    var opts = {};

    if ($(".ctools-use-modal.ctools-modal-follow-style").length > 0 && Drupal.settings['follow-style']) {
      $.extend(true, opts, Drupal.settings['follow-style']);
    }

    var defaults = {
      modalTheme: 'CToolsModalDialog',
      throbberTheme: 'CToolsModalThrobber',
      animation: 'show',
      animationSpeed: 'fast',
      modalSize: {
        type: 'scale',
        width: .8,
        height: .8,
        addWidth: 0,
        addHeight: 0,
        // How much to remove from the inner content to make space for the
        // theming.
        contentRight: 25,
        contentBottom: 45
      },
      modalOptions: {
        opacity: .55,
        background: '#fff'
      }
    };

    var settings = {};
    $.extend(true, settings, defaults, Drupal.settings.CToolsModal, opts);

    if (Drupal.CTools.Modal.currentSettings && Drupal.CTools.Modal.currentSettings != settings) {
      Drupal.CTools.Modal.modal.remove();
      Drupal.CTools.Modal.modal = null;
    }

    Drupal.CTools.Modal.currentSettings = settings;

    var resize = function(e) {
      // When creating the modal, it actually exists only in a theoretical
      // place that is not in the DOM. But once the modal exists, it is in the
      // DOM so the context must be set appropriately.
      var context = e ? document : Drupal.CTools.Modal.modal;

      var modalSize = Drupal.CTools.Modal.currentSettings.modalSize;
      if (modalSize.width && modalSize.width > 0 && modalSize.width <= 1) {
        var width = $(window).width() * modalSize.width;
      } else {
        var width = modalSize.width;
      }
      if (modalSize.height && modalSize.height > 0 && modalSize.height <= 1) {
        var height = $(window).height() * modalSize.height;
      } else {
        var height = modalSize.height;
      }

      // Use the additionol pixels for creating the width and height.
      $('div.ctools-modal-content', context).css({
        'width': width + modalSize.addWidth + 'px',
        'height': height + modalSize.addHeight + 'px'
      });
      $('div.ctools-modal-content .modal-content', context).css({
        'width': (width - modalSize.contentRight) + 'px',
        'height': (height - modalSize.contentBottom) + 'px'
      });
    }

    if (!Drupal.CTools.Modal.modal) {
      Drupal.CTools.Modal.modal = $(Drupal.theme(settings.modalTheme));
      if (settings.modalSize.height <= 1 || settings.modalSize.width <= 1) {
        $(window).bind('resize', resize);
      }
    }

    resize();

    $('span.modal-title', Drupal.CTools.Modal.modal).html(Drupal.CTools.Modal.currentSettings.loadingText);
    Drupal.CTools.Modal.modalContent(Drupal.CTools.Modal.modal, settings.modalOptions, settings.animation, settings.animationSpeed);
    $('#modalContent .modal-content').html(Drupal.theme(settings.throbberTheme));
  };

  /**
   * Provide the HTML to create the modal dialog.
   */
  Drupal.theme.prototype.CToolsModalDialog = function () {
    var html = ''
    html += '  <div id="ctools-modal">'
    html += '    <div class="ctools-modal-content">' // panels-modal-content
    html += '      <div class="modal-header">';
    html += '        <a class="close" href="#">';
    html +=            Drupal.CTools.Modal.currentSettings.closeText + Drupal.CTools.Modal.currentSettings.closeImage;
    html += '        </a>';
    html += '        <span id="modal-title" class="modal-title">&nbsp;</span>';
    html += '      </div>';
    html += '      <div id="modal-content" class="modal-content">';
    html += '      </div>';
    html += '    </div>';
    html += '  </div>';

    return html;
  }

  /**
   * Provide the HTML to create the throbber.
   */
  Drupal.theme.prototype.CToolsModalThrobber = function () {
    var html = '';
    html += '  <div id="modal-throbber">';
    html += '    <div class="modal-throbber-wrapper">';
    html +=        Drupal.CTools.Modal.currentSettings.throbber;
    html += '    </div>';
    html += '  </div>';

    return html;
  };

})(jQuery);
