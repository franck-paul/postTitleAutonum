/*global dotclear */
'use strict';

window.addEventListener('load', () => {
  dotclear.pta = dotclear.getData('pta_options');
  dotclear.pta.suggest = '';

  const input = document.querySelector('#post_title');

  input.onblur = () => {
    const title = input.value;
    if (title === '') {
      dotclear.pta.suggest = '';
    } else {
      dotclear.services(
        'suggestTitle',
        (data) => {
          try {
            const response = JSON.parse(data);
            if (response?.success) {
              if (
                response?.payload.ret &&
                dotclear.pta.suggest !== response.payload.suggest &&
                window.confirm(response.payload.msg)
              ) {
                input.value = response.payload.suggest;
              }
            } else {
              console.log(dotclear.debug && response?.message ? response.message : 'Dotclear REST server error');
              return;
            }
          } catch (e) {
            console.log(e);
          }
        },
        (error) => {
          console.log(error);
        },
        true, // Use GET method
        {
          json: 1, // Use JSON format for payload
          type: dotclear.pta.post_type,
          title,
        },
      );
    }
  };
});
