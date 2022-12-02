const listeners = {};

/**
 * Calls any listeners attached to the given event and calls them with the given arguments
 *
 * @param {string} event
 * @param {array} args
 */
function trigger(event, args) {
  listeners[event] = listeners[event] || [];
  listeners[event].forEach((f) => f.apply(null, args));
}

/**
 * Attaches a new listener to the given event
 *
 * @param {string} event
 * @param {function} func
 */
function on(event, func) {
  listeners[event] = listeners[event] || [];
  listeners[event].push(func);
}

/**
 * Removes the listener from the given event
 *
 * @param {string} event
 * @param {function} func
 */
function off(event, func) {
  listeners[event] = listeners[event] || [];
  listeners[event] = listeners[event].filter((f) => f !== func);
}

export default { trigger, on, off };
