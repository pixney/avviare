<?php

namespace Pixney\AvviareExtension\Command;

/**
 * Class TailwindWebpack
 *
 *  @author Pixney AB <hello@pixney.com>
 *  @author William Åström <william@pixney.com>
 *
 *  @link https://pixney.com
 */
class TailwindWebpack
{
    public function __construct($themePath, $chosenScaffoldType)
    {
    }

    public function handle()
    {
        $pathToScssFile     = '.' . str_replace(base_path(), '', $themePath) . '/resources/sass/theme.scss';
        $pathToTailwindConf = '.' . str_replace(base_path(), '', $themePath) . '/resources/sass/tailwind.config.js';

        $webpack    = $this->filesystem->get($this->extPath . "/resources/stubs/themes/{$chosenScaffoldType}/webpack.mix.js");
        $webpack    = str_replace('DummyAppCSS', $pathToScssFile, $webpack);
        $webpack    = str_replace('DummyTailwindConfPath', $pathToTailwindConf, $webpack);
        $this->filesystem->put(base_path('webpack.mix.js'), $webpack);
    }
}
