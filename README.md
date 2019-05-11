# About

This extension will help you keep the regular laravel structure using mix
when developing websites/themes with Pyrocms.


## How to use it

### Install pyro cms
```
composer create-project pyrocms/pyrocms [projectname]
php artisan install
```

[For more information, see PyroCMS documentation](https://pyrocms.com/documentation/pyrocms/3.7/getting-started/installation)



### Install Avviare

```
composer require "pixney/avviare-extension"
composer dump
php artisan addon:install avviare
``` 

### Create a theme
Behind the scenes, this will run pyrocms artisan command for scaffolding a theme, then simply remove and replace files to make sure it works the way we want.
```
php artisan make:theme mycompany.theme.themename
``` 


### SVG Spitemaps
If you use svgs, it's a recommended to create a svg sprite. Install this package and then place your svg's within `resources/svgs`. When you run `npm run watch/production` this plugin will create the svg sprite map for you.
```
npm install svg-spritemap-webpack-plugin
```

#### Using an svg within twig files
Include the spritemap in your view:
```
<div style="display:none">
	{% include "theme::partials/svgs" %}
</div>
```

To display the svg :

```
<svg><use xlink:href="#example" /></svg>
```

### Use Browsersync
Within the webpack.mix.js file, set the proxy and files to watch for:

```
.browserSync({
    proxy: 'devsite.test',
    files: [
        'public/js/**/*.js',
        'public/css/**/*.css'
    ]
})
```


**If you don't want to use svg sprite plugin or browsersync, simply comment out or remove the reference within webpack.mix.js**


## After install

Run `npm install` from the root directory (basepath) to install all the dependencies specified in your `package.json` file.

Then you can simply run your normal `npm run watch` or `npm run production` commands.

### Make sure you are using the theme.
Either to to display->settings within the admin to activate your theme or in your .env file specify:
`STANDARD_THEME=mycompany.theme.themename`

### Change the Open Graph and Favicon images
Within your theme directory, you want to change the images placed within `resources/images`. You can change it's location as well, but if you do make sure you update metadata.twig.

