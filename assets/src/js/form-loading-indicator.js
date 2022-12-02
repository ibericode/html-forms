function getButtonText(button) {
  return button.innerHTML ? button.innerHTML : button.value;
}

function setButtonText(button, text) {
  if (button.innerHTML) {
    button.innerHTML = text;
  } else {
    button.value = text;
  }
}

/**
 * Creates a new loading indicator
 * @param {HTMLFormElement} formEl
 * @param {string} char
 * @returns {{stop: stop, start: start}}
 * @constructor
 */
function Loader(formEl, char) {
  const button = formEl.querySelector('input[type="submit"], button[type="submit"]');
  char = char || '\u00B7';
  let originalButton; let
    loadingInterval;

  if (button) {
    originalButton = button.cloneNode(true);
  }

  /**
   * Starts the loading indicator
   */
  function start() {
    if (button) {
      const loadingText = button.getAttribute('data-loading-text');
      if (loadingText) {
        // Use text from "data-loading-text" attribute
        setButtonText(button, loadingText);
      } else {
        // Use default loading character (. .. ...)
        button.style.width = window.getComputedStyle(button).width;
        setButtonText(button, char);
        loadingInterval = window.setInterval(tick, 500);
      }
    } else {
      // If form had no button element, change opacity
      formEl.style.opacity = '0.5';
    }

    if (formEl.classList) {
      formEl.classList.add('mc4wp-loading');
    }
  }

  /**
   * Stops the loading indicator
   */
  function stop() {
    if (button) {
      button.style.width = originalButton.style.width;
      const text = getButtonText(originalButton);
      setButtonText(button, text);
      window.clearInterval(loadingInterval);
    } else {
      formEl.style.opacity = '';
    }

    formEl.className = formEl.className.replace('mc4wp-loading', '');
  }

  /**
   * Represents a single animation frame.
   * By default, this adds a dot to the button text.
   */
  function tick() {
    const text = getButtonText(button);
    setButtonText(button, text.length >= 5 ? char : `${text} ${char}`);
  }

  return {
    start,
    stop,
  };
}

module.exports = Loader;
