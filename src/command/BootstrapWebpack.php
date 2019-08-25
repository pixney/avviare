<?php

namespace Pixney\AvviareExtension\Command;

use Illuminate\Filesystem\Filesystem;

/**
 * Class BootstrapWebpack
 *
 *  @author Pixney AB <hello@pixney.com>
 *  @author William Åström <william@pixney.com>
 *
 *  @link https://pixney.com
 */
class BootstrapWebpack
{
    /**
     * Path of our extension
     *
     * @var string
     */
    protected $extPath;

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

    /**
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param string $extPath
     * @param string $themePath
     * @param string $chosenScaffoldType
     */
    public function __construct(string $extPath, string $themePath, string $chosenScaffoldType)
    {
        $this->extPath            = $extPath;
        $this->themePath          = $themePath;
        $this->chosenScaffoldType = $chosenScaffoldType;
        $this->filesystem         = app(Filesystem::class);
    }

    public function handle()
    {
        $jsPath                    = '.' . str_replace(base_path(), '', $this->themePath) . '/resources/js/app.js';
        $cssPath                   = '.' . str_replace(base_path(), '', $this->themePath) . '/resources/sass/theme.scss';
        $DummySvgSpriteDestination = '..' . str_replace(base_path(), '', $this->themePath) . '/resources/views/partials/svgs.twig';
        $DummySvgSourcePath        = '.' . str_replace(base_path(), '', $this->themePath) . '/resources/assets/svgs/*.svg';

        $webpack    = $this->filesystem->get($this->extPath . "/resources/stubs/themes/{$this->chosenScaffoldType}/webpack.mix.js");
        $webpack    = str_replace('DummyAppJS', $jsPath, $webpack);
        $webpack    = str_replace('DummyAppCSS', $cssPath, $webpack);
        $webpack    = str_replace('DummySvgSpriteDestination', $DummySvgSpriteDestination, $webpack);
        $webpack    = str_replace('DummySvgSourcePath', $DummySvgSourcePath, $webpack);
        $this->filesystem->put(base_path('webpack.mix.js'), $webpack);
    }
}
