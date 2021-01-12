(function () { var require = undefined; var module = undefined; var exports = undefined; var define = undefined;(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

function getFieldValues(form, fieldName, evt) {
  var values = [];
  var inputs = form.querySelectorAll('input[name="' + fieldName + '"], select[name="' + fieldName + '"], textarea[name="' + fieldName + '"], button[name="' + fieldName + '"]');

  for (var i = 0; i < inputs.length; i++) {
    var input = inputs[i];
    var type = input.type.toLowerCase();

    if ((type === 'radio' || type === 'checkbox') && !input.checked) {
      continue;
    } // ignore buttons which are not clicked (in case there's more than one button with same name)


    if (type === 'button' || type === 'submit' || input.tagName === 'BUTTON') {
      if ((!evt || evt.target !== input) && form.dataset[fieldName] !== input.value) {
        continue;
      }

      form.dataset[fieldName] = input.value;
    }

    values.push(input.value);
  } // default to an empty string
  // can be used to show or hide an element when a field is empty or has not been set
  // Usage: data-show-if="FIELDNAME:"


  if (values.length === 0) {
    values.push('');
  }

  return values;
}

function findForm(element) {
  var bubbleElement = element;

  while (bubbleElement.parentElement) {
    bubbleElement = bubbleElement.parentElement;

    if (bubbleElement.tagName === 'FORM') {
      return bubbleElement;
    }
  }

  return null;
}

function toggleElement(el, evt) {
  var show = !!el.getAttribute('data-show-if');
  var conditions = show ? el.getAttribute('data-show-if').split(':') : el.getAttribute('data-hide-if').split(':');
  var fieldName = conditions[0];
  var expectedValues = (conditions.length > 1 ? conditions[1] : '*').split('|');
  var form = findForm(el);
  var values = getFieldValues(form, fieldName, evt); // determine whether condition is met

  var conditionMet = false;

  for (var i = 0; i < values.length; i++) {
    var value = values[i]; // condition is met when value is in array of expected values OR expected values contains a wildcard and value is not empty

    conditionMet = expectedValues.indexOf(value) > -1 || expectedValues.indexOf('*') > -1 && value.length > 0;

    if (conditionMet) {
      break;
    }
  } // toggle element display


  if (show) {
    el.style.display = conditionMet ? '' : 'none';
  } else {
    el.style.display = conditionMet ? 'none' : '';
  } // find all inputs inside this element and toggle [required] attr (to prevent HTML5 validation on hidden elements)


  var inputs = el.querySelectorAll('input, select, textarea');
  [].forEach.call(inputs, function (el) {
    if ((conditionMet || show) && el.getAttribute('data-was-required')) {
      el.required = true;
      el.removeAttribute('data-was-required');
    }

    if ((!conditionMet || !show) && el.required) {
      el.setAttribute('data-was-required', 'true');
      el.required = false;
    }
  });
} // evaluate conditional elements globally


function evaluate() {
  var elements = document.querySelectorAll('.hf-form [data-show-if], .hf-form [data-hide-if]');
  [].forEach.call(elements, toggleElement);
} // re-evaluate conditional elements for change events on forms


function handleInputEvent(evt) {
  if (!evt.target || !evt.target.form || evt.target.form.className.indexOf('hf-form') < 0) {
    return;
  }

  var form = evt.target.form;
  var elements = form.querySelectorAll('[data-show-if], [data-hide-if]');
  [].forEach.call(elements, function (el) {
    return toggleElement(el, evt);
  });
}

var _default = {
  init: function init() {
    document.addEventListener('click', handleInputEvent, true);
    document.addEventListener('keyup', handleInputEvent, true);
    document.addEventListener('change', handleInputEvent, true);
    document.addEventListener('hf-refresh', evaluate, true);
    window.addEventListener('load', evaluate);
    evaluate();
  }
};
exports["default"] = _default;

},{}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;
var listeners = {};

function trigger(event, args) {
  listeners[event] = listeners[event] || [];
  listeners[event].forEach(function (f) {
    return f.apply(null, args);
  });
}

function on(event, func) {
  listeners[event] = listeners[event] || [];
  listeners[event].push(func);
}

function off(event, func) {
  listeners[event] = listeners[event] || [];
  listeners[event] = listeners[event].filter(function (f) {
    return f !== func;
  });
}

var _default = {
  trigger: trigger,
  on: on,
  off: off
};
exports["default"] = _default;

},{}],3:[function(require,module,exports){
'use strict';

function getButtonText(button) {
  return button.innerHTML ? button.innerHTML : button.value;
}

function setButtonText(button, text) {
  button.innerHTML ? button.innerHTML = text : button.value = text;
}

function Loader(formElement) {
  this.form = formElement;
  this.button = formElement.querySelector('input[type="submit"], button[type="submit"]');
  this.loadingInterval = 0;
  this.character = "\xB7";

  if (this.button) {
    this.originalButton = this.button.cloneNode(true);
  }
}

Loader.prototype.setCharacter = function (c) {
  this.character = c;
};

Loader.prototype.start = function () {
  if (this.button) {
    // loading text
    var loadingText = this.button.getAttribute('data-loading-text');

    if (loadingText) {
      setButtonText(this.button, loadingText);
      return;
    } // Show AJAX loader


    var styles = window.getComputedStyle(this.button);
    this.button.style.width = styles.width;
    setButtonText(this.button, this.character);
    this.loadingInterval = window.setInterval(this.tick.bind(this), 500);
  } else {
    this.form.style.opacity = '0.5';
  }
};

Loader.prototype.tick = function () {
  // count chars, start over at 5
  var text = getButtonText(this.button);
  var loadingChar = this.character;
  setButtonText(this.button, text.length >= 5 ? loadingChar : text + ' ' + loadingChar);
};

Loader.prototype.stop = function () {
  if (this.button) {
    this.button.style.width = this.originalButton.style.width;
    var text = getButtonText(this.originalButton);
    setButtonText(this.button, text);
    window.clearInterval(this.loadingInterval);
  } else {
    this.form.style.opacity = '';
  }
};

module.exports = Loader;

},{}],4:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _iterableToArrayLimit(arr, i) { if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e2) { throw _e2; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e3) { didErr = true; err = _e3; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

var populate = require('populate.js'); // parse ?query=string with array support. no nesting.


function parseUrlParams(q) {
  var params = new URLSearchParams(q);
  var obj = {};

  var _iterator = _createForOfIteratorHelper(params.entries()),
      _step;

  try {
    for (_iterator.s(); !(_step = _iterator.n()).done;) {
      var _step$value = _slicedToArray(_step.value, 2),
          name = _step$value[0],
          value = _step$value[1];

      if (name.substr(name.length - 2) === '[]') {
        var arrName = name.substr(0, name.length - 2);
        obj[arrName] = obj[arrName] || [];
        obj[arrName].push(value);
      } else {
        obj[name] = value;
      }
    }
  } catch (err) {
    _iterator.e(err);
  } finally {
    _iterator.f();
  }

  return obj;
}

function init() {
  if (!window.URLSearchParams) {
    return;
  } // only act on form elements outputted by HTML Forms


  var forms = [].filter.call(document.forms, function (f) {
    return f.className.indexOf('hf-form') > -1;
  });

  if (!forms) {
    return;
  } // fill each form with data from URL params


  var data = parseUrlParams(window.location.search);
  forms.forEach(function (f) {
    populate(f, data);
  });
}

var _default = {
  init: init
};
exports["default"] = _default;

},{"populate.js":7}],5:[function(require,module,exports){
"use strict";

/* window.CustomEvent polyfill for IE */
(function () {
  if (typeof window.CustomEvent === 'function') return false;

  function CustomEvent(event, params) {
    params = params || {
      bubbles: false,
      cancelable: false,
      detail: undefined
    };
    var evt = document.createEvent('CustomEvent');
    evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
    return evt;
  }

  CustomEvent.prototype = window.Event.prototype;
  window.CustomEvent = CustomEvent;
})();

},{}],6:[function(require,module,exports){
"use strict";

var _formPrefiller = _interopRequireDefault(require("./form-prefiller.js"));

var _conditionality = _interopRequireDefault(require("./conditionality.js"));

require("./polyfills/custom-event.js");

var _events = _interopRequireDefault(require("./events.js"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var Loader = require('./form-loading-indicator.js');

var vars = window.hf_js_vars || {
  ajax_url: window.location.href
};

function cleanFormMessages(formEl) {
  var messageElements = formEl.querySelectorAll('.hf-message');
  [].forEach.call(messageElements, function (el) {
    el.parentNode.removeChild(el);
  });
}

function addFormMessage(formEl, message) {
  var txtElement = document.createElement('p');
  txtElement.className = 'hf-message hf-message-' + message.type;
  txtElement.innerHTML = message.text; // uses innerHTML because we allow some HTML strings in the message settings

  txtElement.role = 'alert';
  var wrapperElement = formEl.querySelector('.hf-messages') || formEl;
  wrapperElement.appendChild(txtElement);
}

function handleSubmitEvents(e) {
  var formEl = e.target;

  if (formEl.className.indexOf('hf-form') < 0) {
    return;
  } // always prevent default (because regular submit doesn't work for HTML Forms)


  e.preventDefault();
  submitForm(formEl);
}

function submitForm(formEl) {
  cleanFormMessages(formEl);
  emitEvent('submit', formEl);
  var formData = new FormData(formEl);
  [].forEach.call(formEl.querySelectorAll('[data-was-required=true]'), function (el) {
    formData.append('_was_required[]', el.getAttribute('name'));
  });
  var request = new XMLHttpRequest();
  request.onreadystatechange = createRequestHandler(formEl);
  request.open('POST', vars.ajax_url, true);
  request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  request.send(formData);
  request = null;
}

function emitEvent(eventName, element) {
  // browser event API: formElement.on('hf-success', ..)
  element.dispatchEvent(new CustomEvent('hf-' + eventName)); // custom events API: html_forms.on('success', ..)

  _events["default"].trigger(eventName, [element]);
}

function createRequestHandler(formEl) {
  var loader = new Loader(formEl);
  loader.start();
  return function () {
    // are we done?
    if (this.readyState === 4) {
      var response;
      loader.stop();

      if (this.status >= 200 && this.status < 400) {
        try {
          response = JSON.parse(this.responseText);
        } catch (error) {
          console.log('HTML Forms: failed to parse AJAX response.\n\nError: "' + error + '"');
          return;
        }

        emitEvent('submitted', formEl);

        if (response.error) {
          emitEvent('error', formEl);
        } else {
          emitEvent('success', formEl);
        } // Show form message


        if (response.message) {
          addFormMessage(formEl, response.message);
          emitEvent('message', formEl);
        } // Should we hide form?


        if (response.hide_form) {
          formEl.querySelector('.hf-fields-wrap').style.display = 'none';
        } // Should we redirect?


        if (response.redirect_url) {
          window.location = response.redirect_url;
        } // clear form


        if (!response.error) {
          formEl.reset();
        }
      } else {
        // Server error :(
        console.log(this.responseText);
      }
    }
  };
}

document.addEventListener('submit', handleSubmitEvents, false); // useCapture=false to ensure we bubble upwards (and thus can cancel propagation)

_conditionality["default"].init();

_formPrefiller["default"].init();

window.html_forms = {
  on: _events["default"].on,
  off: _events["default"].off,
  submit: submitForm
};

},{"./conditionality.js":1,"./events.js":2,"./form-loading-indicator.js":3,"./form-prefiller.js":4,"./polyfills/custom-event.js":5}],7:[function(require,module,exports){

/**
 * Populate form fields from a JSON object.
 *
 * @param form object The form element containing your input fields.
 * @param data array JSON data to populate the fields with.
 * @param basename string Optional basename which is added to `name` attributes
 */
function populate(form, data, basename) {
	for (var key in data) {
		if (! data.hasOwnProperty(key)) {
			continue;
		}

		var name = key;
		var value = data[key];

        if ('undefined' === typeof value) {
            value = '';
        }

        if (null === value) {
            value = '';
        }

		// handle array name attributes
		if (typeof(basename) !== "undefined") {
			name = basename + "[" + key + "]";
		}

		if (value.constructor === Array) {
			name += '[]';
		} else if(typeof value == "object") {
			populate(form, value, name);
			continue;
		}

		// only proceed if element is set
		var element = form.elements.namedItem(name);
		if (! element) {
			continue;
		}

		var type = element.type || element[0].type;

		switch(type ) {
			default:
				element.value = value;
				break;

			case 'radio':
			case 'checkbox':
				var values = value.constructor === Array ? value : [value];
				for (var j=0; j < element.length; j++) {
					element[j].checked = values.indexOf(element[j].value) > -1;
				}
				break;

			case 'select-multiple':
				var values = value.constructor === Array ? value : [value];
				for(var k = 0; k < element.options.length; k++) {
					element.options[k].selected = (values.indexOf(element.options[k].value) > -1 );
				}
				break;

			case 'select':
			case 'select-one':
				element.value = value.toString() || value;
				break;

			case 'date':
      			element.value = new Date(value).toISOString().split('T')[0];	
				break;
		}

	}
};

if (typeof module !== 'undefined' && module.exports) {
	module.exports = populate;
} 
},{}]},{},[6]);
; })();