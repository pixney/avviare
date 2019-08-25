/*
 |--------------------------------------------------------------------------
 | Avviare Extension by Pixney AB
 |--------------------------------------------------------------------------
 | 
 | The following has been added when you ran the Avviare extension.
 | 
 */

let mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');


mix
  .disableSuccessNotifications()
  .sass('DummyAppCSS', 'css')
  .options({
    processCssUrls: false,
    postCss: [tailwindcss(DummyTailwindConfPath)],
  })
  .sourceMaps().version();




