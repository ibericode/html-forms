const listeners = {}

function trigger (event, args) {
  listeners[event] = listeners[event] || []
  listeners[event].forEach(f => f.apply(null, args))
}

function on (event, func) {
  listeners[event] = listeners[event] || []
  listeners[event].push(func)
}

function off (event, func) {
  listeners[event] = listeners[event] || []
  listeners[event] = listeners[event].filter(f => f !== func)
}

export default { trigger, on, off }
