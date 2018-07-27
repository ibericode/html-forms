'use strict';

const populate = require('populate.js');

// parse ?query=string with array support. no nesting.
function parseUrlParams(q) {    
	let params = new URLSearchParams(q);
	let obj = {};
	for(let [name, value] of params.entries()) {
		if(name.substr(name.length-2) === "[]") {
			let arrName = name.substr(0, name.length-2);
			obj[arrName] = obj[arrName] || [];
			obj[arrName].push(value);
		} else {
			obj[name] = value;
		}
	  	
	}
	return obj;
}

function init() {
	// only act on form elements outputted by HTML Forms
	let forms = [].filter.call(document.forms, (f) => f.className.indexOf('hf-form') > -1);	
	if(!forms) {
		return;
	}

	// fill each form with data from URL params
	let data = parseUrlParams(window.location.search);
	forms.forEach((f) => {
		populate(f, data);
	})
}

export default { init }
