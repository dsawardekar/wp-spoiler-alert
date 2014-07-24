var webpack = require('webpack');

module.exports = {
  entry: {
    app: './app/main.js',
    vendor: './app/vendor.js',
  },
  output: {
    path: 'dist/assets',
    filename: '[name].js'
  },
  plugins: [
    new webpack.optimize.CommonsChunkPlugin('vendor', 'vendor.js', Infinity)
  ],
  externals: {
    'jquery': 'jQuery'
  },
  module: {
    loaders: [
      { test: /\.js$/, loader: 'jsx-loader' }
    ],
  }
};
