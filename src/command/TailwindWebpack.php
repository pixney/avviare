<?php

namespace Pixney\AvviareExtension\Command;

use Illuminate\Filesystem\Filesystem;

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
    /**
     * Path to our created theme
     *
     * @var string
     */
    protected $themePath;

    /**
     * The type of scaffold chosen
     *
     * @var string
     */
    protected $chosenScaffoldType;

    public function __construct($themePath, $chosenScaffoldType)
    {
        $this->themePath          = $themePath;
        $this->chosenScaffoldType = $chosenScaffoldType;
        $this->filesystem         = app(Filesystem::class);
    }

    public function handle()
    {
        $pathToScssFile     = '.' . str_replace(base_path(), '', $this->themePath) . '/resources/sass/theme.scss';
        $pathToTailwindConf = '.' . str_replace(base_path(), '', $this->themePath) . '/resources/sass/tailwind.config.js';

        $webpack    = $this->filesystem->get($this->extPath . "/resources/stubs/themes/{$this->chosenScaffoldType}/webpack.mix.js");
        $webpack    = str_replace('DummyAppCSS', $pathToScssFile, $webpack);
        $webpack    = str_replace('DummyTailwindConfPath', $pathToTailwindConf, $webpack);
        $this->filesystem->put(base_path('webpack.mix.js'), $webpack);
    }
}
