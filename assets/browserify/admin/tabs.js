let tabs, tabNavs

function init () {
  tabs = document.querySelectorAll('.hf-tab')
  tabNavs = document.querySelectorAll('#hf-tabs-nav a')
  for (let i = 0; i < tabNavs.length; i++) {
    tabNavs[i].addEventListener('click', open)
  }
}

function open (e) {
  const tabTarget = this.getAttribute('data-tab-target')
  for (let i = 0; i < tabNavs.length; i++) {
    tabNavs[i].classList.toggle('nav-tab-active', tabNavs[i] === this)
  }
  this.blur()

  for (let i = 0; i < tabs.length; i++) {
    const tab = tabs[i]
    tab.classList.toggle('hf-tab-active', tab.getAttribute('data-tab') === tabTarget)
  }

  document.title = document.title.replace(document.title.split(' - ').shift(), this.innerText + ' ')

  if (window.history) {
    let newUrl = window.location.href
    newUrl = newUrl.replace(/&tab=\w+/, '')
    newUrl += '&tab=' + tabTarget

    window.history.replaceState({ tab: tabTarget }, document.title, newUrl)
  }

  e.preventDefault()
}

export default {
  init,
  open
}
