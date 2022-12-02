/**
 * @param {MouseEvent} evt
 */
function handleTabLinkClick(evt) {
  const tabTarget = this.getAttribute('data-tab-target');
  for (let i = 0; i < tabNavs.length; i++) {
    tabNavs[i].classList.toggle('nav-tab-active', tabNavs[i] === this);
  }
  this.blur();

  for (let i = 0; i < tabs.length; i++) {
    const tab = tabs[i];
    tab.classList.toggle('hf-tab-active', tab.getAttribute('data-tab') === tabTarget);
  }

  document.title = document.title.replace(document.title.split(' - ').shift(), `${this.innerText} `);

  if (window.history) {
    let newUrl = window.location.href;
    newUrl = newUrl.replace(/&tab=\w+/, '');
    newUrl += `&tab=${tabTarget}`;

    window.history.replaceState({ tab: tabTarget }, document.title, newUrl);
  }

  evt.preventDefault();
}

const tabs = document.querySelectorAll('.hf-tab');
const tabNavs = document.querySelectorAll('#hf-tabs-nav a');
for (let i = 0; i < tabNavs.length; i++) {
  tabNavs[i].addEventListener('click', handleTabLinkClick);
}
