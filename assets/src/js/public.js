import './form-prefiller.js'
import './conditionality.js'
import './polyfills/custom-event.js'
import events from './events.js'
const Loader = require('./form-loading-indicator.js')

const vars = window.hf_js_vars || { ajax_url: window.location.href }

function cleanFormMessages (formEl) {
  const messageElements = formEl.querySelectorAll('.hf-message');
  [].forEach.call(messageElements, (el) => {
    el.parentNode.removeChild(el)
  })
}

function addFormMessage (formEl, message) {
  const txtElement = document.createElement('p')
  txtElement.className = 'hf-message hf-message-' + message.type
  txtElement.innerHTML = message.text // uses innerHTML because we allow some HTML strings in the message settings
  txtElement.role = 'alert'

  const wrapperElement = formEl.querySelector('.hf-messages') || formEl
  wrapperElement.appendChild(txtElement)
}

function handleSubmitEvents (e) {
  const formEl = e.target
  if (formEl.className.indexOf('hf-form') < 0) {
    return
  }

  // always prevent default (because regular submit doesn't work for HTML Forms)
  e.preventDefault()
  submitForm(formEl)
}

function submitForm (formEl) {
  cleanFormMessages(formEl)
  emitEvent('submit', formEl)

  const formData = new FormData(formEl);
  [].forEach.call(formEl.querySelectorAll('[data-was-required=true]'), function (el) {
    formData.append('_was_required[]', el.getAttribute('name'))
  })

  let request = new XMLHttpRequest()
  request.onreadystatechange = createRequestHandler(formEl)
  request.open('POST', vars.ajax_url, true)
  request.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
  request.send(formData)
  request = null
}

function emitEvent (eventName, element) {
  // browser event API: formElement.on('hf-success', ..)
  element.dispatchEvent(new CustomEvent('hf-' + eventName))

  // custom events API: html_forms.on('success', ..)
  events.trigger(eventName, [element])
}

function createRequestHandler (formEl) {
  const loader = new Loader(formEl)
  loader.start()

  return function () {
    // are we done?
    if (this.readyState === 4) {
      let response
      loader.stop()

      if (this.status >= 200 && this.status < 400) {
        try {
          response = JSON.parse(this.responseText)
        } catch (error) {
          console.log('HTML Forms: failed to parse AJAX response.\n\nError: "' + error + '"')
          return
        }

        emitEvent('submitted', formEl)

        if (response.error) {
          emitEvent('error', formEl)
        } else {
          emitEvent('success', formEl)
        }

        // Show form message
        if (response.message) {
          addFormMessage(formEl, response.message)
          emitEvent('message', formEl)
        }

        // Should we hide form?
        if (response.hide_form) {
          formEl.querySelector('.hf-fields-wrap').style.display = 'none'
        }

        // Should we redirect?
        if (response.redirect_url) {
          window.location = response.redirect_url
        }

        // clear form
        if (!response.error) {
          formEl.reset()
        }
      } else {
        // Server error :(
        console.log(this.responseText)
      }
    }
  }
}

document.addEventListener('submit', handleSubmitEvents, false) // useCapture=false to ensure we bubble upwards (and thus can cancel propagation)

window.html_forms = {
  on: events.on,
  off: events.off,
  submit: submitForm
}
