const DrupalTranslationsWebpackPlugin = require('drupal-translations-webpack-plugin')
const path = require('path')

module.exports = (_env, argv) => {
  const production = argv.mode !== 'development'
  return {
    entry: {
      dashboard: './components/dashboard/bootstrap.js',
    },
    output: {
      filename: '[name].js',
      path: path.resolve(__dirname, 'dist')
    },
    devtool: production ? 'none' : 'inline-source-map',
    devServer: {
      contentBase: './'
    },
    optimization: {
      runtimeChunk: 'single',
      splitChunks: {
        name: () => 'bundle',
        chunks: 'all'
      }
    },
    module: {
      rules: [
        {
          test: /\.(js|jsx)$/,
          exclude: /node_modules/,
          use: {
            loader: "babel-loader"
          }
        },
        {
          test: /\.css$/,
          exclude: /node_modules/,
          use: ['style-loader', 'css-loader']
        }
      ],
    },
    plugins: [
      new DrupalTranslationsWebpackPlugin({
        output: 'translations.js'
      })
    ]
  }
}
