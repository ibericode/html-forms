const populate = require('populate.js');

/**
 * parse ?query=string with array support. no nesting.
 * @param {string} q
 * @returns {{}}
 */
function parseUrlParams(q) {
  const params = new URLSearchParams(q);
  const obj = {};
  const entries = params.entries();
  for (let i = 0; i < entries.length; i++) {
    const [name, value] = entries[i];
    if (name.substring(name.length - 2) === '[]') {
      const arrName = name.substring(0, name.length - 2);
      obj[arrName] = obj[arrName] || [];
      obj[arrName].push(value);
    } else {
      obj[name] = value;
    }
  }
  return obj;
}

// only act on form elements outputted by HTML Forms
const forms = [].filter.call(document.forms, (f) => f.className.indexOf('hf-form') > -1);
if (forms) {
  // fill each form with data from URL params
  const data = parseUrlParams(window.location.search);
  forms.forEach((f) => {
    populate(f, data);
  });
}
