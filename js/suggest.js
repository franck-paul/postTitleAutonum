/*global dotclear */
'use strict';

window.addEventListener('load', () => {
  dotclear.pta = dotclear.getData('pta_options');
  dotclear.pta.suggest = '';

  const input = document.querySelector('#post_title');

  input.addEventListener('blur', () => {
    const title = input.value;
    if (title === '') {
      dotclear.pta.suggest = '';
    } else {
      dotclear.jsonServicesGet(
        'suggestTitle',
        (payload) => {
          if (payload.ret && dotclear.pta.suggest !== payload.suggest && dotclear.confirm(payload.msg)) {
            input.value = payload.suggest;
          }
        },
        {
          type: dotclear.pta.post_type,
          title,
        },
      );
    }
  });
});
