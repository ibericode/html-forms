let availableActions, actionTemplates, actions

function init () {
  actions = document.getElementById('hf-form-actions')
  availableActions = document.getElementById('hf-available-form-actions')
  actionTemplates = document.getElementById('hf-form-action-templates');

  // turn settings into accordions
  [].forEach.call(actions.querySelectorAll('.hf-action-settings'), function (el) {
    el.parentNode.removeChild(el)

    let heading = el.getAttribute('data-title')
    const summary = el.querySelector('.hf-action-summary')
    if (summary) {
      heading += ' &mdash; <span class="hf-muted">' + summary.innerHTML + '</span>'
    }
    const wrap = createAccordion(heading, el.innerHTML)
    actions.appendChild(wrap)

    actions.querySelector('#hf-form-actions-empty').style.display = 'none'
  })

  availableActions.addEventListener('click', addAction, true)
}

function createAccordion (headingHTML, contentHTML) {
  const wrap = document.createElement('div')
  wrap.className = 'hf-accordion expanded '

  const heading = document.createElement('h4')
  heading.className = 'hf-accordion-heading'
  heading.innerHTML = headingHTML
  wrap.appendChild(heading)

  const content = document.createElement('div')
  content.className = 'hf-accordion-content'
  content.innerHTML = contentHTML
  wrap.appendChild(content)

  const deleteWrap = document.createElement('p')
  deleteWrap.style.textAlign = 'right'
  const deleteLink = document.createElement('a')
  deleteLink.href = 'javascript:void(0);'
  deleteLink.className = 'danger'
  deleteLink.innerText = 'Delete this action'
  deleteWrap.appendChild(deleteLink)
  content.appendChild(deleteWrap)

  // bind handlers
  heading.addEventListener('click', createToggleActionHandler(wrap, content))
  deleteLink.addEventListener('click', createDeleteActionHandler(wrap))
  return wrap
}

function addAction (e) {
  const el = e.target || e.srcElement
  if (el.tagName !== 'INPUT') {
    return
  }

  const actionType = el.getAttribute('data-action-type')
  const actionTemplate = actionTemplates.querySelector(`#hf-action-type-${actionType}-template`)

  // append HTML to actions wrapper
  const index = actions.querySelectorAll('div').length - 1
  const wrap = createAccordion(el.value, actionTemplate.innerHTML.replace(/\$index/g, index))
  actions.appendChild(wrap)

  // hide "no form actions" message
  actions.querySelector('#hf-form-actions-empty').style.display = 'none'

  window.html_forms.Editor.updateFieldVariables()
}

function createDeleteActionHandler (wrap) {
  return function () {
    actions.removeChild(wrap)

    if (actions.childElementCount === 1) {
      actions.querySelector('#hf-form-actions-empty').style.display = ''
    }
  }
}

function createToggleActionHandler (wrap, content) {
  return function () {
    const show = content.offsetParent === null
    wrap.className = wrap.className.replace('expanded', '') + (show ? ' expanded' : '')
    content.style.display = show ? 'block' : 'none'
  }
}

export default { init }
