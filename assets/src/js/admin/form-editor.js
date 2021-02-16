// load CodeMirror & plugins
const CodeMirror = require('codemirror')
require('codemirror/mode/xml/xml')
require('codemirror/mode/javascript/javascript')
require('codemirror/mode/css/css')
require('codemirror/mode/htmlmixed/htmlmixed')
require('codemirror/addon/fold/xml-fold')
require('codemirror/addon/edit/matchtags')
require('codemirror/addon/edit/closetag.js')

let editor, element, dom, requiredFieldsInput, emailFieldsInput, previewFrame, previewDom
const templateRegex = /\{\{ *(\w+)(?:\.([\w.]+))? *(?:\|\| *(\w+))? *\}\}/g

function init () {
  previewFrame = document.getElementById('hf-form-preview')
  element = document.getElementById('hf-form-editor')
  dom = document.createElement('form')
  requiredFieldsInput = document.getElementById('hf-required-fields')
  emailFieldsInput = document.getElementById('hf-email-fields')

  dom.innerHTML = element.value
  editor = CodeMirror.fromTextArea(element, {
    selectionPointer: true,
    matchTags: { bothTags: true },
    mode: 'htmlmixed',
    htmlMode: true,
    autoCloseTags: true,
    autoRefresh: true,
    styleActiveLine: true,
    matchBrackets: true
  })

  editor.on('changes', debounce(updatePreview, 500))
  editor.on('changes', debounce(updateShadowDOM, 100))
  editor.on('changes', debounce(updateFieldVariables, 500))
  editor.on('blur', updatePreview)
  editor.on('blur', updateShadowDOM)
  editor.on('blur', updateFieldVariables)
  editor.on('blur', updateRequiredFields)
  editor.on('blur', updateEmailFields)

  previewFrame.addEventListener('load', setPreviewDom)
  setPreviewDom()
  updateFieldVariables()
  window.addEventListener('load', updateFieldVariables)
}

function setPreviewDom () {
  const frameContent = previewFrame.contentDocument || previewFrame.contentWindow.document
  previewDom = frameContent.querySelector('.hf-fields-wrap')

  if (previewDom) {
    updatePreview()
  }
}

function getFieldVariableName (f) {
  return f.name.replace('[]', '').replace(/\[(\w+)\]/g, '.$1')
}

function updateFieldVariables () {
  const fields = dom.querySelectorAll('input[name], select[name], textarea[name], button[name]')
  const fieldVariables = uniq([].map.call(fields, (f) => '[' + getFieldVariableName(f) + ']'))
  const wpbody = document.getElementById('wpbody-content');

  [].forEach.call(document.querySelectorAll('.hf-field-names'), (el) => {
    // remove existing variables
    while (el.firstChild) {
      el.removeChild(el.firstChild)
    }

    const variableElements = fieldVariables.map((n) => {
      // measure width of actual font size for prettiness
      const sizeEl = document.createElement('span')
      sizeEl.style.visibility = 'hidden'
      sizeEl.innerText = n
      wpbody.appendChild(sizeEl)
      const width = sizeEl.offsetWidth
      wpbody.removeChild(sizeEl)

      // add input el
      const el = document.createElement('input')
      el.type = 'text'
      el.style.maxWidth = Math.ceil((width * 1.1) + 14) + 'px'
      el.value = n
      el.readonly = true
      el.onfocus = () => el.select()
      return el
    })

    variableElements.forEach((vel, i, arr) => {
      el.appendChild(vel)
    })
  })
}

function updatePreview () {
  let markup = editor.getValue()

  // replace template tags
  markup = markup.replace(templateRegex, function (s, m) {
    // if a default value was provided, use that
    if (arguments[3]) {
      return arguments[3]
    }

    return ''
  })

  // update dom
  previewDom.innerHTML = markup
  previewDom.dispatchEvent(new Event('hf-refresh'))
}

function updateShadowDOM () {
  dom.innerHTML = editor.getValue()
}

function updateRequiredFields () {
  const fields = dom.querySelectorAll('[required]')
  const fieldNames = [].map.call(fields, getFieldVariableName)
  requiredFieldsInput.value = fieldNames.join(',')
}

function updateEmailFields () {
  const fields = dom.querySelectorAll('input[type="email"]')
  const fieldNames = [].map.call(fields, getFieldVariableName)
  emailFieldsInput.value = fieldNames.join(',')
}

function replaceSelection (str) {
  editor.replaceSelection(str)
  editor.focus()
}

function debounce (func, wait, immediate) {
  var timeout
  return function () {
    var context = this; var args = arguments
    var later = function () {
      timeout = null
      if (!immediate) func.apply(context, args)
    }
    var callNow = immediate && !timeout
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    if (callNow) func.apply(context, args)
  }
};

function uniq (a) {
  var seen = {}
  return a.filter(function (item) {
    return seen.hasOwnProperty(item) ? false : (seen[item] = true)
  })
}

export default {
  init,
  replaceSelection,
  updateFieldVariables
}
