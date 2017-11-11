const path = require('path');
module.exports = {
  entry: './clientsrc/index.js',
  output: {
    path: path.resolve('assets'),
    filename: 'trigger-happy.js'
  },
  externals: {
   jquery: 'jQuery'
 },
  module: {
    loaders: [
      { test: /\.js$/, loader: [
          "babel-loader"
      ], include: [path.resolve('clientsrc'),path.resolve('jsclient')] },
      { test: /\.jsx$/, loader: [
          "babel-loader"
      ], include: [path.resolve('clientsrc'),path.resolve('jsclient')] }
    ]
  }
}
