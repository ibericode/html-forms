const populate = require('populate.js')

// parse ?query=string with array support. no nesting.
function parseUrlParams (q) {
  const params = new URLSearchParams(q)
  const obj = {}
  for (const [name, value] of params.entries()) {
    if (name.substr(name.length - 2) === '[]') {
      const arrName = name.substr(0, name.length - 2)
      obj[arrName] = obj[arrName] || []
      obj[arrName].push(value)
    } else {
      obj[name] = value
    }
  }
  return obj
}

(function () {
  if (!window.URLSearchParams) {
    return
  }

  // only act on form elements outputted by HTML Forms
  const forms = [].filter.call(document.forms, f => f.className.indexOf('hf-form') > -1)
  if (!forms) {
    return
  }

  // fill each form with data from URL params
  const data = parseUrlParams(window.location.search)
  forms.forEach(f => {
    populate(f, data)
  })
})()
