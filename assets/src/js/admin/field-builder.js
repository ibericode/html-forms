// imports
import { h, render } from 'preact';
import { FieldBuilder } from './components/field-builder.js';

/**
 *
 * @param {string} key
 * @param {string} label
 * @param {array} configRows
 * @constructor
 */
function Field(key, label, configRows) {
  this.key = key;
  this.label = label;
  this.configRows = configRows || [];
}

function mount() {
  render(<FieldBuilder fields={fields} />, document.getElementById('hf-field-builder'));
}

// bootstrap
const fields = [
  new Field('text', 'Text', ['label', 'placeholder', 'default-value', 'required', 'wrap', 'add-to-form']),
  new Field('email', 'Email', ['label', 'placeholder', 'default-value', 'required', 'wrap', 'add-to-form']),
  new Field('url', 'URL', ['label', 'placeholder', 'default-value', 'required', 'wrap', 'add-to-form']),
  new Field('number', 'Number', ['label', 'placeholder', 'default-value', 'required', 'wrap', 'add-to-form']),
  new Field('date', 'Date', ['label', 'default-value', 'required', 'wrap', 'add-to-form']),
  new Field('textarea', 'Textarea', ['label', 'placeholder', 'default-value', 'required', 'wrap', 'add-to-form']),
  new Field('dropdown', 'Dropdown', ['label', 'choices', 'multiple', 'required', 'wrap', 'add-to-form']),
  new Field('checkbox', 'Checkboxes', ['label', 'choices', 'wrap', 'add-to-form']),
  new Field('radio', 'Radio buttons', ['label', 'choices', 'wrap', 'add-to-form']),
  new Field('submit', 'Submit button', ['button-text', 'wrap', 'add-to-form']),
];

export default {
  init() {
    mount();
  },

  registerField(key, label, configRows) {
    fields.push(new Field(key, label, configRows));
  },
};
