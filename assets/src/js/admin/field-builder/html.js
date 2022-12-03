import { h } from 'preact';
import render from 'preact-render-to-string';

function htmlgenerate(conf) {
  const fieldName = namify(conf.fieldLabel);
  const fieldId = `${conf.formSlug}-${fieldName}`;
  const label = conf.fieldLabel.length && conf.fieldType !== 'submit' ? h('label', {
    for: fieldId,
  }, conf.fieldLabel) : '';
  let fieldAttr; let
    field;

  switch (conf.fieldType) {
    case 'textarea':
      fieldAttr = {
        name: fieldName,
        placeholder: conf.placeholder,
        required: conf.required,
        id: fieldId,
      };
      field = html('textarea', fieldAttr, conf.value);
      break;

    case 'dropdown':
      fieldAttr = {
        name: fieldName + (conf.multiple ? '[]' : ''),
        required: conf.required,
        multiple: conf.multiple,
        id: fieldId,
      };
      field = html('select', fieldAttr, conf.choices.map((choice) => (
        html('option', { selected: choice.checked }, choice.label)
      )));
      break;

    case 'radio':
      field = conf.choices.map((choice) => (
        html('label', {}, [
          html('input', {
            type: 'radio',
            name: fieldName,
            value: choice.label,
            selected: choice.checked,
          }),
          ' ',
          html('span', {}, choice.label),
        ])
      ));
      break;

    case 'checkbox':
      field = conf.choices.map((choice) => (
        html('label', {}, [
          html('input', {
            type: 'checkbox',
            name: `${fieldName}[]`,
            value: choice.label,
            checked: choice.checked,
          }),
          ' ',
          html('span', {}, choice.label),
        ])
      ));
      break;

    case 'file':
      fieldAttr = {
        type: 'file',
        name: fieldName,
        required: conf.required,
        id: fieldId,
      };

      if (conf.accept) {
        fieldAttr.accept = conf.accept;
      }

      field = html('input', fieldAttr);
      break;

    case 'submit':
      fieldAttr = {
        type: 'submit',
        value: conf.value,
      };
      field = html('input', fieldAttr);
      break;
    case 'text':
    default:
      fieldAttr = {
        type: conf.fieldType,
        name: fieldName,
        value: conf.value,
        placeholder: conf.placeholder,
        required: conf.required,
        id: fieldId,
      };
      field = html('input', fieldAttr);
      break;
  }

  let str = '';

  if (conf.wrap) {
    const children = label !== '' ? ['\n\t', label, '\n\t', field, '\n'] : ['\n\t', field, '\n'];
    const tmpl = h('p', {}, children);
    str = renderToString(tmpl);
  } else {
    const children = label !== '' ? [label, '\n', field] : [field];
    str = renderToString(children);
  }

  return str;
}

function renderToString(vdom) {
  return render(vdom, { pretty: true });
}

function html(tag, attr, children) {
  attr = filterEmptyObjectValues(attr);
  return h(tag, attr, children);
}

/**
 * Returns a string that is valid for name attributes
 * @param {string} str
 * @returns {string}
 */
function namify(str) {
  return str.replace(/ /g, '_').replace(/[^\w[\]_]*/g, '').toUpperCase();
}

/**
 * Returns the given object with all empty values stripped
 * @param {{}} obj
 * @returns {{}}
 */
function filterEmptyObjectValues(obj) {
  const newObj = {};
  Object.keys(obj).forEach((key) => {
    if (obj[key] !== false && obj[key] !== '') {
      newObj[key] = obj[key];
    }
  });
  return newObj;
}

export default htmlgenerate;
