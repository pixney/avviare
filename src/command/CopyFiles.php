<?php

namespace Pixney\AvviareExtension\Command;

use Illuminate\Filesystem\Filesystem;

/**
 * Class CopyFiles
 *
 *  @author Pixney AB <hello@pixney.com>
 *  @author William Åström <william@pixney.com>
 *
 *  @link https://pixney.com
 */
class CopyFiles
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
     * @param string $themePath
     * @param string $chosenScaffoldType
     */
    public function __construct(string $extPath, string $themePath, string $chosenScaffoldType)
    {
        $this->filesystem         = app(Filesystem::class);
        $this->themePath          = $themePath;
        $this->chosenScaffoldType = $chosenScaffoldType;
        $this->extPath            = $extPath;
    }

    public function handle()
    {
        // Copy files
        $from = $this->extPath . "/resources/stubs/themes/{$this->chosenScaffoldType}/resources";
        $to   = "{$this->themePath}/resources";
        $this->filesystem->copyDirectory($from, $to);

        // Copy Package Json
        $packagejson    = $this->filesystem->get($this->extPath . "/resources/stubs/themes/{$this->chosenScaffoldType}/package.json");
        $this->filesystem->put(base_path('package.json'), $packagejson);
    }
}
