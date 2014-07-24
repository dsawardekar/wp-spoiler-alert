var webpack = require('webpack');

module.exports = {
  entry: {
    app: './app/vendor.js'
  },
  output: {
    path: 'dist/assets',
    filename: 'vendor.js'
  },
  module: {
    loaders: [
      { test: /\.js$/, loader: 'jsx-loader' }
    ]
  }
};
