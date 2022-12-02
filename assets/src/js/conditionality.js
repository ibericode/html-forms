/**
 * @param {HTMLFormElement} form
 * @param {string} fieldName
 * @param {Event|null} evt
 * @returns {*[]}
 */
function getFieldValues(form, fieldName, evt) {
  const values = [];
  const inputs = form.querySelectorAll(`input[name="${fieldName}"], select[name="${fieldName}"], textarea[name="${fieldName}"], button[name="${fieldName}"]`);

  for (const input of inputs) {
    const type = input.type.toLowerCase();

    if ((type === 'radio' || type === 'checkbox') && (!input.checked)) {
      continue;
    }

    // ignore buttons which are not clicked (in case there's more than one button with same name)
    if (type === 'button' || type === 'submit' || input.tagName === 'BUTTON') {
      if ((!evt || evt.target !== input) && form.dataset[fieldName] !== input.value) {
        continue;
      }

      form.dataset[fieldName] = input.value;
    }

    values.push(input.value);
  }

  // default to an empty string
  // can be used to show or hide an element when a field is empty or has not been set
  // Usage: data-show-if="FIELDNAME:"
  if (values.length === 0) {
    values.push('');
  }

  return values;
}

/**
 * Finds closest <form> element from the given element or any of its parents
 * @param {HTMLElement} element
 * @returns {HTMLFormElement|null}
 */
function findForm(element) {
  let bubbleElement = element;

  while (bubbleElement.parentElement) {
    bubbleElement = bubbleElement.parentElement;

    if (bubbleElement.tagName === 'FORM') {
      return bubbleElement;
    }
  }

  return null;
}

/**
 * Toggles visibility of the given element
 * @param {HTMLElement} el
 * @param {Event} evt
 */
function toggleElement(el, evt) {
  const show = !!el.getAttribute('data-show-if');
  const conditions = show ? el.getAttribute('data-show-if').split(':') : el.getAttribute('data-hide-if').split(':');
  const fieldName = conditions[0];
  const expectedValues = ((conditions.length > 1 ? conditions[1] : '*').split('|'));
  const form = findForm(el);
  const values = getFieldValues(form, fieldName, evt);

  // determine whether condition is met
  let conditionMet = false;
  for (let i = 0; i < values.length; i++) {
    const value = values[i];

    // condition is met when value is in array of expected values OR expected values contains a wildcard and value is not empty
    conditionMet = expectedValues.indexOf(value) > -1 || (expectedValues.indexOf('*') > -1 && value.length > 0);

    if (conditionMet) {
      break;
    }
  }

  // toggle element display
  if (show) {
    el.style.display = (conditionMet) ? '' : 'none';
  } else {
    el.style.display = (conditionMet) ? 'none' : '';
  }

  // find all inputs inside this element and toggle [required] attr
  // this prevents HTML5 validation on hidden elements
  const inputs = el.querySelectorAll('input, select, textarea');
  for (const input of inputs) {
    if ((conditionMet || show) && input.getAttribute('data-was-required')) {
      input.required = true;
      input.removeAttribute('data-was-required');
    }

    if ((!conditionMet || !show) && input.required) {
      input.setAttribute('data-was-required', 'true');
      input.required = false;
    }
  }
}

// evaluate conditional elements globally
function evaluate() {
  const elements = document.querySelectorAll('.hf-form [data-show-if], .hf-form [data-hide-if]');
  [].forEach.call(elements, toggleElement);
}

/**
 * re-evaluate conditional elements for change events on forms
 * @param {Event} evt
 */
function handleInputEvent(evt) {
  if (!evt.target || !evt.target.form || evt.target.form.className.indexOf('hf-form') < 0) {
    return;
  }

  const { form } = evt.target;
  const elements = form.querySelectorAll('[data-show-if], [data-hide-if]');
  for (const el of elements) {
    toggleElement(el, evt);
  }
}

document.addEventListener('click', handleInputEvent);
document.addEventListener('keyup', handleInputEvent);
document.addEventListener('change', handleInputEvent);
document.addEventListener('hf-refresh', evaluate);
document.addEventListener('swup:contentReplaced', evaluate);
window.addEventListener('load', evaluate);
evaluate();
