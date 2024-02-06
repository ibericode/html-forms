/**
 * @param {MouseEvent} evt
 */
function handleClickEvent(evt) {
  const t = evt.target;
  if (t.tagName !== 'A') {
    return;
  }

  if (t.hasAttribute('data-hf-confirm')) {
    if (window.confirm(t.getAttribute('data-hf-confirm'))) {
      return;
    }

    evt.preventDefault();
  }
}
document.body.addEventListener('click', handleClickEvent, true);
