# About

Tailwind and Bootstrap theme setup generator for Pyrocms.

I have written this extension simply to help myself get started more quickly and in
a way i prefer to have things setup myself.

If you like it you are free to use it.

## How to use it

### Install pyro cms

```
composer create-project pyrocms/pyrocms [projectname]
php artisan install
```

[For more information, see PyroCMS documentation](https://pyrocms.com/documentation/pyrocms/3.7/getting-started/installation)

### Install Avviare

Within your composer.json file, add :

```
{
  "repositories": [{
    "type": "composer",
    "url": "https://packages.pixney.com"
  }]
}
```

Then simply require it :

```
composer require "pixney/avviare-extension"
composer dump
php artisan addon:install avviare
```

### Create a theme

Running this command, we will be scaffolding the default theme supplied by Pyrocms, and then remove and replace
files to make sure we have a barebone theme setup the way we like it.

```
php artisan avviare:create mycompany.theme.themename
```

_Example: pixney.theme.mytheme_

### SVG Spitemaps (svg-spritemap-webpack-plugin)

If you use svgs, it's a recommended to create a svg sprite. After you have run npm install you simply place your svgs in `resources/svgs` and when you run `npm run watch/production` this plugin will create the svg sprite map for you.

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

### Offside JS

Using an off-canvas navigation called offside.

### Styling

Simple styling, just remove it and make your own..

![Image is showing the theme styling](https://github.com/pixney/avviare-extension/blob/master/resources/stubs/images/theme.png)

## After install

Run `npm install` from the root directory (basepath) to install all the dependencies specified in your `package.json` file.

Then you can simply run your normal `npm run watch` or `npm run production` commands.

### Make sure you are using the theme.

Either to to display->settings within the admin to activate your theme or in your .env file specify:
`STANDARD_THEME=mycompany.theme.themename`

### Change the Open Graph and Favicon images

Within your theme directory, you want to change the images placed within `resources/images`. You can change it's location as well, but if you do make sure you update metadata.twig.
