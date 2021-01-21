'use strict'

function getButtonText (button) {
  return button.innerHTML ? button.innerHTML : button.value
}

function setButtonText (button, text) {
  button.innerHTML ? button.innerHTML = text : button.value = text
}

function Loader (formElement) {
  this.form = formElement
  this.button = formElement.querySelector('input[type="submit"], button[type="submit"]')
  this.loadingInterval = 0
  this.character = '\u00B7'

  if (this.button) {
    this.originalButton = this.button.cloneNode(true)
  }
}

Loader.prototype.setCharacter = function (c) {
  this.character = c
}

Loader.prototype.start = function () {
  if (this.button) {
    // loading text
    const loadingText = this.button.getAttribute('data-loading-text')
    if (loadingText) {
      setButtonText(this.button, loadingText)
      return
    }

    // Show AJAX loader
    const styles = window.getComputedStyle(this.button)
    this.button.style.width = styles.width
    setButtonText(this.button, this.character)
    this.loadingInterval = window.setInterval(this.tick.bind(this), 500)
  } else {
    this.form.style.opacity = '0.5'
  }
}

Loader.prototype.tick = function () {
  // count chars, start over at 5
  const text = getButtonText(this.button)
  const loadingChar = this.character
  setButtonText(this.button, text.length >= 5 ? loadingChar : text + ' ' + loadingChar)
}

Loader.prototype.stop = function () {
  if (this.button) {
    this.button.style.width = this.originalButton.style.width
    const text = getButtonText(this.originalButton)
    setButtonText(this.button, text)
    window.clearInterval(this.loadingInterval)
  } else {
    this.form.style.opacity = ''
  }
}

module.exports = Loader
