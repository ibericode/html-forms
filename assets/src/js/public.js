import './form-prefiller.js';
import './conditionality.js';
import events from './events.js';
import Loader from './form-loading-indicator.js';

/**
 * Remove all elements with class "hf-message" from the given form element
 *
 * @param {HTMLFormElement} formEl
 */
function cleanFormMessages(formEl) {
  const messageElements = formEl.querySelectorAll('.hf-message');
  for (const el of messageElements) {
    el.parentNode.removeChild(el);
  }
}

/**
 * Add response message to the given form element
 *
 * @param {HTMLFormElement} formEl
 * @param {object} message
 */
function addFormMessage(formEl, message) {
  const txtElement = document.createElement('p');
  txtElement.className = `hf-message hf-message-${message.type}`;
  // use innerHTML because we allow some HTML strings in the message settings
  txtElement.innerHTML = message.text;
  txtElement.role = 'alert';

  const wrapperElement = formEl.querySelector('.hf-messages') || formEl;
  wrapperElement.appendChild(txtElement);
}

/**
 * Handler for submit events for any <form> on this page
 *
 * @param {SubmitEvent} evt
 */
function handleSubmitEvents(evt) {
  const formEl = evt.target;
  if (formEl.className.indexOf('hf-form') < 0) {
    return;
  }

  // always prevent default (because regular submit doesn't work for HTML Forms)
  evt.preventDefault();
  submitForm(formEl);
}

/**
 * Submits the given <form> element to our AJAX endpoint.
 *
 * @param {HTMLFormElement} formEl
 */
function submitForm(formEl) {
  cleanFormMessages(formEl);
  emitEvent('submit', formEl);

  const formData = new FormData(formEl);
  const requiredFields = formEl.querySelectorAll('[data-was-required=true]');
  for (const el of requiredFields) {
    formData.append('_was_required[]', el.getAttribute('name'));
  }
  const vars = window.hf_js_vars || { ajax_url: window.location.href };
  let request = new XMLHttpRequest();
  request.onreadystatechange = createRequestHandler(formEl);
  request.open('POST', vars.ajax_url, true);
  request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  request.send(formData);
  request = null;
}

/**
 * Fire event with the given name
 *
 * @param {string} eventName
 * @param {HTMLFormElement} formEl
 */
function emitEvent(eventName, formEl) {
  // browser event API: formElement.on('hf-success', ..)
  formEl.dispatchEvent(new CustomEvent(`hf-${eventName}`));

  // custom events API: html_forms.on('success', ..)
  events.trigger(eventName, [formEl]);
}

/**
 * Creates the request handler for XmlHttpRequest
 *
 * @param {HTMLFormElement} formEl
 * @returns {(function(): void)|*}
 */
function createRequestHandler(formEl) {
  const loader = new Loader(formEl);
  loader.start();

  return function () {
    // are we done?
    if (this.readyState === 4) {
      let response;
      loader.stop();

      if (this.status >= 200 && this.status < 400) {
        try {
          response = JSON.parse(this.responseText);
        } catch (error) {
          // eslint-disable-next-line no-console
          console.log(`HTML Forms: failed to parse AJAX response.\n\nError: "${error}"`);
          return;
        }

        emitEvent('submitted', formEl);

        if (response.error) {
          emitEvent('error', formEl);
        } else {
          emitEvent('success', formEl);
        }

        // Show form message
        if (response.message) {
          addFormMessage(formEl, response.message);
          emitEvent('message', formEl);
        }

        // Should we hide form?
        if (response.hide_form) {
          formEl.querySelector('.hf-fields-wrap').style.display = 'none';
        }

        // Should we redirect?
        if (response.redirect_url) {
          window.location = response.redirect_url;
        }

        // clear form
        if (!response.error) {
          formEl.reset();
        }
      } else {
        // Server error :(
        // eslint-disable-next-line no-console
        console.log(this.responseText);
      }
    }
  };
}

// protect against loading script twice
if (window.html_forms === undefined) {
  document.addEventListener('submit', handleSubmitEvents, false); // useCapture=false to ensure we bubble upwards (and thus can cancel propagation)

  window.html_forms = {
    on: events.on,
    off: events.off,
    submit: submitForm,
  };
}
