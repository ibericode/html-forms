'use strict';

// Get fields based on name from a form.
function getFields(form, fieldName) {
    return form.querySelectorAll('input[name="'+fieldName+'"], select[name="'+fieldName+'"], textarea[name="'+fieldName+'"], button[name="'+fieldName+'"]');
}

function getFieldValues(form, fieldName) {
    let values = [];
    let inputs = getFields(form, fieldName);

    for(let i=0; i<inputs.length; i++) {
        const input = inputs[i];
        const type = input.getAttribute("type");

        if( ( type === "radio" || type === "checkbox" ) && ( ! input.checked ) ) {
            continue;
        }

        if( type === 'button' || type === 'submit' || input.tagName === 'BUTTON' ) {
            if ( ( ! evt || evt.target !== input ) && form.dataset[fieldName] !== input.value ) {
                continue;
            }

            form.dataset[fieldName] = input.value;
        } 
    
        values.push(input.value);
    }

    return values;
}

function findForm(element) {
    let bubbleElement = element;

    while(bubbleElement.parentElement) {
        bubbleElement = bubbleElement.parentElement;

        if(bubbleElement.tagName === 'FORM') {
            return bubbleElement;
        }
    }

    return null;
}

function toggleElement(el, evt) {
    const show = !!el.getAttribute('data-show-if');
    const conditions = show ? el.getAttribute('data-show-if').split(':') : el.getAttribute('data-hide-if').split(':');
    const fieldName = conditions[0];
    const expectedValues = ((conditions.length > 1 ? conditions[1] : "*").split('|'));
    const form = findForm(el);
    const values = getFieldValues(form, fieldName, evt);

    // determine whether condition is met
    let conditionMet = false;
    for(let i=0; i<values.length; i++) {
        const value = values[i];

        // condition is met when value is in array of expected values OR expected values contains a wildcard and value is not empty
        conditionMet = expectedValues.indexOf(value) > -1 || ( expectedValues.indexOf('*') > -1 && value.length > 0 );

        if(conditionMet) {
            break;
        }
    }

    // toggle element display
    if(show){
        el.style.display = ( conditionMet ) ? '' : 'none';
    } else {
        el.style.display = ( conditionMet ) ? 'none' : '';
    }

    // find all inputs inside this element and toggle [required] attr (to prevent HTML5 validation on hidden elements)
    let inputs = el.querySelectorAll('input, select, textarea');
    [].forEach.call(inputs, (el) => {
        if(( conditionMet || show  ) && el.getAttribute('data-was-required')) {
            el.required = true;
            el.removeAttribute('data-was-required');
        }

        if(( !conditionMet || ! show ) && el.required) {
           el.setAttribute('data-was-required', "true");
           el.required = false;
        }
    });
}

function selectOption(el, evt) {
    var conditions = el.getAttribute('data-select-if').split(':');
    var conditionKey = conditions[0];
    var expectedValues = (conditions.length > 1 ? conditions[1] : "*").split('|');
    var form = findForm(el);
    var values = getFields(form, conditionKey).length ? getFieldValues(form, conditionKey) : [conditionKey];

    // determine whether condition is met
    var conditionMet = false;
    for (var i = 0; i < values.length; i++) {
        var value = values[i];

        // condition is met when value is in array of expected values OR expected values contains a wildcard and value is not empty
        conditionMet = expectedValues.indexOf(value) > -1 || expectedValues.indexOf('*') > -1 && value.length > 0;
        if (conditionMet) {
            break;
        }
    }

    // Select/check option(s)
    if (conditionMet) {
        el.parentElement.value = el.value;
    }

}

function checkOptions(el, evt) {
    var conditions = el.getAttribute('data-check-if').split(':');
    var conditionKey = conditions[0];
    var expectedValues = (conditions.length > 1 ? conditions[1] : "*").split('|');
    var form = findForm(el);
    var values = getFields(form, conditionKey).length ? getFieldValues(form, conditionKey) : [conditionKey];

    // determine whether condition is met
    var conditionMet = false;
    for (var i = 0; i < values.length; i++) {
        var value = values[i];

        // condition is met when value is in array of expected values OR expected values contains a wildcard and value is not empty
        conditionMet = expectedValues.indexOf(value) > -1 || expectedValues.indexOf('*') > -1 && value.length > 0;
        if (conditionMet) {
            break;
        }
    }

    // Select/check option(s)
    if (conditionMet) {
        console.log( el );
        el.checked = true;
    }

}

// evaluate conditional elements globally
function evaluate() {
    const elements = document.querySelectorAll('.hf-form [data-show-if], .hf-form [data-hide-if]');
    [].forEach.call(elements, toggleElement);

    // auto-select
    var selectIfElements = document.querySelectorAll('.hf-form [data-select-if]');
    [].forEach.call(selectIfElements, selectOption);

    // auto-check
    var checkIfElements = document.querySelectorAll('.hf-form [data-check-if]');
    [].forEach.call(checkIfElements, checkOptions);
}

// re-evaluate conditional elements for change events on forms
function handleInputEvent(evt) {
    if( ! evt.target || ! evt.target.form || evt.target.form.className.indexOf('hf-form') < 0 ) {
        return;
    }

    const form = evt.target.form;
    const elements = form.querySelectorAll('[data-show-if], [data-hide-if]');
    [].forEach.call(elements, (el) => toggleElement(el, evt));

    // auto-select
    const selectIfElements = form.querySelectorAll('.hf-form [data-select-if]');
    [].forEach.call(selectIfElements, (el) => selectOption(el, evt));

    // auto-check
    var checkIfElements = form.querySelectorAll('.hf-form [data-check-if]');
    [].forEach.call(checkIfElements, (el) => checkOptions(el, evt));

}

export default {
    'init': function() {
        document.addEventListener('click', handleInputEvent, true);
        document.addEventListener('keyup', handleInputEvent, true);
        document.addEventListener('change', handleInputEvent, true);
        document.addEventListener('hf-refresh', evaluate, true);
        window.addEventListener('load', evaluate);
        evaluate();
    }
}
