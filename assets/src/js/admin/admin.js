import Tabs from './tabs.js'
import Editor from './form-editor.js'
import Actions from './form-actions.js'
import FieldBuilder from './field-builder.js'
import Confirmations from './action-confirmations.js'
import tlite from 'tlite'

window.html_forms = {}

// init the various components
Tabs.init()
Confirmations.init()

if (document.getElementById('hf-form-editor')) {
  Editor.init()
  Actions.init()
  FieldBuilder.init(Editor)
}

tlite(el => el.className.indexOf('hf-tooltip') > -1)

window.html_forms.FieldBuilder = FieldBuilder
window.html_forms.Editor = Editor

// tell WP common.js to override the method used for determining hidden columns (screen options)
if (window.hf_options.view === 'edit') {
  window.columns.useCheckboxesForHidden()
}
