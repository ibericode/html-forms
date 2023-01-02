const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');
const postcss = require('postcss');
const cssnano = require('cssnano');

module.exports = {
  entry: {
    public: './assets/src/js/public.js',
    'gutenberg-block': './assets/src/js/gutenberg-block.js',
    admin: './assets/src/js/admin/admin.js',
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'assets/js'),
  },
  module: {
    rules: [
      {
        test: /\.js$/i,
        exclude: [/\/node_modules\//],
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              ['@babel/preset-env', { targets: '> 0.2%, last 2 versions, Firefox ESR, not dead' }],
            ],
            plugins: [
              [
                '@babel/plugin-transform-react-jsx',
                {
                  pragma: 'h',
                },
              ],
            ],
          },
        },
      },
    ],
  },
  plugins: [
    new CopyPlugin({
      patterns: [
        { from: './assets/src/img', to: path.resolve(__dirname, './assets/img') },
        {
          from: './assets/src/css',
          to: path.resolve(__dirname, './assets/css'),
          transform: (content, path) => postcss([cssnano])
            .process(content, {
              from: path,
            })
            .then((result) => result.css),
        },
      ],
    }),
  ],
};
