module.exports = {
  env: {
    browser: true,
    commonjs: true,
    es2021: true,
  },
  extends: ['airbnb-base', 'plugin:react/recommended'],
  overrides: [
  ],
  parserOptions: {
    ecmaVersion: 'latest',
  },
  settings: {
    react: {
      // eslint-plugin-preact interprets this as "h.createElement",
      // however we only care about marking h() as being a used variable.
      pragma: 'h',
      // We use "react 16.0" to avoid pushing folks to UNSAFE_ methods.
      version: '16.0',
    },
  },
  rules: {
    'no-alert': 0,
    'no-mixed-operators': 0,
    'import/extensions': 0,
    'no-shadow': 0,
    'no-plusplus': 0,
    'no-bitwise': 0,
    'no-use-before-define': 0,
    'no-continue': 0,
    'no-param-reassign': 0,
    'func-names': 0,
    'no-nested-ternary': 0,
    'prefer-spread': 0,
    'default-case': 0,

    // react / preact
    'react/react-in-jsx-scope': 0,
    'react/prop-types': 0,
  },
};
