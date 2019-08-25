/*
 |--------------------------------------------------------------------------
 | Avviare Extension by Pixney AB
 |--------------------------------------------------------------------------
 | 
 | The following has been added when you ran the Avviare extension.
 | 
 */

let mix = require('laravel-mix');

let SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');
let svgSourcePath = "DummySvgSourcePath";
let svgSpriteDestination = "DummySvgSpriteDestination";

mix
   .disableSuccessNotifications()
   .js('DummyAppJS', 'js')
   .sass('DummyAppCSS', 'css')

   .browserSync({
      proxy: 'devsite.test',
      files: [
         'public/js/**/*.js',
         'public/css/**/*.css'
      ]
   })

   .webpackConfig({
      plugins: [
         new SVGSpritemapPlugin(
            svgSourcePath, {
               output: {
                  filename: svgSpriteDestination,
                  svgo: {
                     removeTitle: true,
                  }
               },
               sprite: {
                  prefix: false
               }
            }
         )
      ]
   })


   .sourceMaps().version();


