import path from 'path';
import {Configuration, webpack, DefinePlugin} from 'webpack';
import LiveReloadPlugin from 'webpack-livereload-plugin'

const config: Configuration = {
  entry:  './src/Main.tsx',
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        use: 'ts-loader',
        exclude: /node_modules/,
      }
    ]
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js'],
  },
  output: {
    filename: 'bundle.js',
    path: path.resolve(__dirname, 'dist'),
  },
  watchOptions: {
    aggregateTimeout: 200,
    poll: 1000,
  }
}

export default function(env,argv):Configuration{

  config.plugins = [
    new DefinePlugin({
      APP_MODE: JSON.stringify(argv.mode)
    })
  ]

  if (argv.mode === 'development') {
    config.devtool = 'eval-source-map';
    config.watch = true;
    config.mode = 'development';
    config.plugins.push(
      new LiveReloadPlugin({
        delay: 500
      })
    );

  } else {
    config.mode = 'production';
  }


  return config;

}