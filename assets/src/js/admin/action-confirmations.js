/**
 * @param {MouseEvent} evt
 */
function handleClickEvent(evt) {
  if (evt.target.tagName !== 'A') {
    return;
  }

  if (evt.target.hasAttribute('data-hf-confirm')) {
    const sure = confirm(evt.target.getAttribute('data-hf-confirm'));

    if (!sure) {
      evt.preventDefault();
    }
  }
}
document.body.addEventListener('click', handleClickEvent, true);
