module.exports = {
  env: {
	browser: true,
	commonjs: true,
	es6: true
  },
  extends: [
	'standard',
	'standard-preact'
  ],
  globals: {
	Atomics: 'readonly',
	SharedArrayBuffer: 'readonly'
  },
  parserOptions: {
	ecmaFeatures: {
	  jsx: true
	},
	ecmaVersion: 9
  },
  rules: {
	'no-prototype-builtins': 'off',
	'react/react-in-jsx-scope': 'off',
  }
}
