import './tabs.js';
import './action-confirmations.js';

import tlite from 'tlite';
import Editor from './form-editor.js';
import Actions from './form-actions.js';
import FieldBuilder from './field-builder.js';

window.html_forms = {};

// Initialize the various components if we're on the "edit form" page
if (document.getElementById('hf-form-editor')) {
  Editor.init();
  Actions.init();
  FieldBuilder.init();
}

tlite((el) => el.className.indexOf('hf-tooltip') > -1);

window.html_forms.FieldBuilder = FieldBuilder;
window.html_forms.Editor = Editor;

// tell WP common.js to override the method used for determining hidden columns (screen options)
if (window.hf_options.view === 'edit') {
  window.columns.useCheckboxesForHidden();
}
