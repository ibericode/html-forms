'use strict';

import renderToString from 'preact-render-to-string';
import { h } from 'preact';

function htmlgenerate(conf) {
    const fieldName = namify(conf.fieldLabel); 
    const fieldId = conf.formSlug + '-' + fieldName;
    const label = conf.fieldLabel.length && conf.fieldType !== 'submit' ? h("label", {
        "for": fieldId,
    }, conf.fieldLabel ) : "";
    let fieldAttr, field;

    switch(conf.fieldType) {
        case "text":
        default:
            fieldAttr = {
                type: conf.fieldType,
                name: fieldName,
                value: conf.value,
                placeholder: conf.placeholder,
                required: conf.required,
                id: fieldId,
            };
            field = html("input", fieldAttr);
            break;
        case "textarea":
            fieldAttr = {
                name: fieldName,
                placeholder: conf.placeholder,
                required: conf.required,
                id: fieldId,
            };
            field = html("textarea", fieldAttr, conf.value);
            break;

        case "dropdown":
            fieldAttr = {
                name: fieldName,
                required: conf.required,
                id: fieldId,
            };
            const opts = conf.choices.map((choice) => (
                html("option", { selected: choice.checked }, choice.label )
            ));
            field = html("select", fieldAttr, opts);
            break;

        case "radio":
            field = conf.choices.map((choice) => (
                html("label", {}, [
                    html("input", {
                        type:"radio",
                        name: fieldName,
                        value: choice.label,
                        selected: choice.checked,
                    }),
                    " ",
                    html("span", {}, choice.label )
                ])
            ));
            break;

        case "checkbox":
            field = conf.choices.map((choice) => (
                html("label", {}, [
                    html("input", {
                        type: "checkbox",
                        name: fieldName + "[]",
                        value: choice.label,
                        checked: choice.checked,
                    }),
                    " ",
                    html("span", {}, choice.label )
                ])
            ));
            break;

        case "file":
            fieldAttr = {
                type: "file",
                name: fieldName,
                required: conf.required,
                id: fieldId,
            };

            if(conf['accept']) {
                fieldAttr['accept'] = conf['accept'];
            }

            field = html("input", fieldAttr);
            break;    


        case "submit":
            fieldAttr = {
                type: "submit",
                value: conf.value,
            };
            field = html("input", fieldAttr);
            break;

    }


    let str = "";
    if( conf.wrap ) {
        let tmpl = h("p", {}, [label, field]);
        str = renderToString(tmpl, null, { pretty: true });
    } else {
        str += renderToString(label, null, { pretty: true });
        str += "\n";
        str += renderToString(field, null, { pretty: true });
    }

    return str;
}

function html(tag, attr, children) {
    return h(tag, filterEmptyObjectValues(attr), children);
}

function namify(str) {
    return str.replace(/ /g, '_').replace(/[^\w\[\]_]*/g, "").toUpperCase();
}

function filterEmptyObjectValues(obj) {
    let newObj = {};
    for (let propName in obj) {
        if( obj[propName] !== false && obj[propName] !== "" ) {
            newObj[propName] = obj[propName];
        }
    }
    return newObj;
}


export {
    htmlgenerate
}
