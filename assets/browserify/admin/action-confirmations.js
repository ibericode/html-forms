function init () {
  document.body.addEventListener('click', handleClickEvent, true)
}

function handleClickEvent (e) {
  if (e.target.tagName !== 'A') {
    return
  }

  if (e.target.hasAttribute('data-hf-confirm')) {
    const sure = confirm(e.target.getAttribute('data-hf-confirm'))

    if (!sure) {
      e.preventDefault()
    }
  }
}

export default {
  init
}
