<?php

namespace Pixney\AvviareExtension\Command;

use Illuminate\Filesystem\Filesystem;

/**
 * Class DeleteUnwantedAssets
 *
 *  @author Pixney AB <hello@pixney.com>
 *  @author William Åström <william@pixney.com>
 *
 *  @link https://pixney.com
 */
class DeleteUnwantedAssets
{
    /**
     * Path to resources within our created theme.
     *
     * @var string
     */
    protected $themeResourcesPath;

    /**
     * Path to our created theme
     *
     * @var string
     */
    protected $themePath;

    /**
     * Directories we dont want
     *
     * @var array
     */
    protected $unwantedDirectories = [
        'js',
        'fonts',
        'scss',
        'sass',
        'views',
        'css'
    ];

    /**
     * Files we don't want
     *
     * @var array
     */
    protected $unwantedFiles = [
        '/webpack.mix.js',
        '/package.json'
    ];

    /**
     * Array with deleted assets information.
     *
     * @var array
     */
    protected $messages=[];

    /**
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    public function __construct(string $themeResourcesPath, string $themePath)
    {
        $this->filesystem         = app(Filesystem::class);
        $this->themePath          = $themePath;
        $this->themeResourcesPath = $themeResourcesPath;
    }

    public function handle()
    {
        // Delete unwanted directories
        foreach ($this->unwantedDirectories as $dir) {
            $this->filesystem->deleteDirectory($this->themeResourcesPath . $dir);
            array_push($this->messages, "Deleted directory : {$this->themePath}/{$dir}");
        }

        // Delete unwanted Files
        foreach ($this->unwantedFiles as $file) {
            $this->filesystem->delete($this->themePath . $file);
            array_push($this->messages, "Deleted file : {$this->themePath}/{$dir}");
        }

        return $this->messages;
    }
}
