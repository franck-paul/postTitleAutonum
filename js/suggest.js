/*global $, dotclear */
'use strict';

$(function () {
  dotclear.pta = dotclear.getData('pta_options');
  dotclear.pta.suggest = '';

  $('#post_title').on('blur', function () {
    const $input = $(this);
    const title = $input.val();
    if (title !== '') {
      $.get('services.php', {
        f: 'suggestTitle',
        xd_check: dotclear.nonce,
        type: dotclear.pta.post_type,
        title: title,
      })
        .done(function (data) {
          if ($('rsp[status=failed]', data).length > 0) {
            // For debugging purpose only:
            // console.log($('rsp',data).attr('message'));
            window.console.log('Dotclear REST server error');
          } else {
            // ret -> status (true/false), true if a new title is proposed
            const ret = Number($('rsp>tpa', data).attr('ret'));
            if (ret) {
              // suggest -> new title
              const suggest = $('rsp>tpa', data).attr('suggest');
              if (dotclear.pta.suggest !== suggest) {
                dotclear.pta.suggest = suggest;
                // msg -> question
                const msg = $('rsp>tpa', data).attr('msg');
                // Suggests the new title
                if (window.confirm(msg)) {
                  $input.val(suggest);
                }
              }
            }
          }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
          window.console.log(`AJAX ${textStatus} (status: ${jqXHR.status} ${errorThrown})`);
        })
        .always(function () {
          // Nothing here
        });
    } else {
      dotclear.pta.suggest = '';
    }
  });
});
