(function () { var require = undefined; var module = undefined; var exports = undefined; var define = undefined;(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
'use strict';

var __ = window.wp.i18n.__;
var registerBlockType = window.wp.blocks.registerBlockType;
var SelectControl = window.wp.components.SelectControl;
var forms = window.html_forms;
registerBlockType('html-forms/form', {
  title: __('HTML Forms: Form'),
  description: __('Block showing a HTML Forms form'),
  category: 'widgets',
  attributes: {
    slug: {
      type: 'string'
    }
  },
  supports: {
    html: false
  },
  icon: /*#__PURE__*/React.createElement("svg", {
    version: "1.0",
    xmlns: "http://www.w3.org/2000/svg",
    width: "256.000000pt",
    height: "256.000000pt",
    viewBox: "0 0 256.000000 256.000000",
    preserveAspectRatio: "xMidYMid meet"
  }, /*#__PURE__*/React.createElement("g", {
    transform: "translate(0.000000,256.000000) scale(0.100000,-0.100000)",
    fill: "#000000",
    stroke: "none"
  }, /*#__PURE__*/React.createElement("path", {
    d: "M0 1280 l0 -1280 1280 0 1280 0 0 1280 0 1280 -1280 0 -1280 0 0 -1280z m2031 593 c8 -8 9 -34 4 -78 -6 -56 -9 -65 -23 -60 -43 16 -98 15 -132 -2 -50 -26 -72 -72 -78 -159 l-5 -74 92 0 91 0 0 -70 0 -70 -90 0 -90 0 0 -345 0 -345 -90 0 -90 0 0 345 0 345 -55 0 -55 0 0 70 0 70 55 0 55 0 0 38 c0 63 20 153 45 202 54 105 141 152 273 147 45 -2 87 -8 93 -14z m-1291 -288 l0 -235 230 0 230 0 0 235 0 235 90 0 90 0 0 -575 0 -575 -90 0 -90 0 0 260 0 260 -230 0 -230 0 0 -260 0 -260 -90 0 -90 0 0 575 0 575 90 0 90 0 0 -235z"
  }))),
  edit: function edit(props) {
    var options = forms.map(function (f) {
      return {
        label: f.title,
        value: f.slug
      };
    });

    if (!props.attributes.slug && options.length > 0) {
      props.setAttributes({
        slug: options[0].value
      });
    }

    return /*#__PURE__*/React.createElement("div", {
      style: {
        backgroundColor: '#f8f9f9',
        padding: '14px'
      }
    }, /*#__PURE__*/React.createElement(SelectControl, {
      label: __('HTML Forms form'),
      value: props.attributes.slug,
      options: options,
      onChange: function onChange(value) {
        props.setAttributes({
          slug: value
        });
      }
    }));
  },
  // Render nothing in the saved content, because we render in PHP
  save: function save(props) {
    return null; // return `[hf_form slug="${props.attributes.slug}"]`;
  }
});

},{}]},{},[1]);
; })();