# About

This extension will help you keep the regular laravel structure using mix
when developing websites/themes with Pyrocms.


## How to use it

### Install pyro cms
```
composer create-project pyrocms/pyrocms [projectname]
php artisan install
```

[Get Started](https://pyrocms.com/documentation/pyrocms/3.7/getting-started/installation)

### Install Avviare

```
composer require "pixney/avviare-extension"
composer dump
php artisan addon:install avviare
``` 

### Create a theme
```
php artisan make:theme mycompany.theme.themename
``` 

### Use svgs
```
npm install svg-spritemap-webpack-plugin
```

### Use Browsersync





### After install
When everything is completed, your theme is ready to be used. Unless you already have, you need to run `npm install` and after completion you can run the regular `npm run watch/prod` commands.

## Important 1
Don't forget to visit `settings->display` and make sure you select your new theme as the public one.

## Important 2
Because of some changes in the metadata.twig file, you need to either remove the lines for the favicon and open graph image or add those images. If you don't - then you will experience some issues trying to view your site.
