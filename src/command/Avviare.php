<?php

namespace Pixney\AvviareExtension\Command;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Pixney\AvviareExtension\AvviareExtension;
use Anomaly\Streams\Platform\Application\Application;
use Anomaly\Streams\Platform\Addon\Console\Command\MakeAddonPaths;

class Avviare extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:theme {theme} {--shared=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description    = 'Create a new pyrocms theme';
    protected $packageJsonUrl = 'https://raw.githubusercontent.com/laravel/laravel/master/package.json';
    protected $namespace      = '';
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $deleteDirs = [
        'js',
        'fonts',
        'scss',
        'sass',
        'views',
        'css'
    ];

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $createDirs = [
        'sass',
        'js',
        'views'
    ];

    protected $deleteFiles = [
        'webpack.mix.js',
        'package.json'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Filesystem $filesystem, Application $application, AvviareExtension $ext)
    {
        $this->namespace            = $this->argument('theme');

        if (preg_match(' #^[a-zA-Z0-9_]+\.[a-zA-Z_]+\.[a-zA-Z0-9_]+\z#u', $this->namespace) !== 1) {
            throw new \Exception('The namespace should be snake case and formatted this way: {vendor}.{type}.{slug}');
        }

        list($vendor, $type, $slug) = array_map(
            function ($value) {
                return str_slug(strtolower($value), '_');
            },
            explode('.', $this->namespace)
        );

        $path                 = $this->dispatch(new MakeAddonPaths($vendor, $type, $slug, $this));
        $type                 = str_singular($type);
        $avviarePath          = $ext->path;
        $resourcesPath        = $path . '/resources/';
        $distPath             = $resourcesPath . 'dist/';

        Artisan::call('make:addon', [
            'namespace' => $this->namespace
        ]);

        foreach ($this->deleteDirs as $dir) {
            $filesystem->deleteDirectory($resourcesPath . $dir);
            $this->info('Deleted: ' . $resourcesPath . $dir);
        }
        foreach ($this->deleteFiles as $file) {
            $filesystem->delete($path . '/' . $file);
            $this->info('Deleted: ' . $path . '/' . $file);
        }
        foreach ($this->createDirs as $dir) {
            $filesystem->makeDirectory($resourcesPath . $dir);
            $this->info('Created: ' . $resourcesPath . $dir);
        }

        // Copy JS files
        $filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/js',
            "{$path}/resources/js"
        );
        $this->info('Javascript files copied');

        // Copy SCSS files
        $filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/sass',
            "{$path}/resources/sass"
        );
        $this->info('Sass files copied');

        // Copy VIEWS files
        $filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/views',
            "{$path}/resources/views"
        );

        // Copy Svg files
        $filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/svgs',
            "{$path}/resources/assets/svgs"
        );

        // Copy Image files
        $filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/images',
            "{$path}/resources/images"
        );

        if ($this->confirm('Would you like us to automatically set your webpack.mix.js file?')) {
            $jsPath                    = '.' . str_replace(base_path(), '', $path) . '/resources/js/app.js';
            $cssPath                   = '.' . str_replace(base_path(), '', $path) . '/resources/sass/theme.scss';
            $DummySvgSpriteDestination = '.' . str_replace(base_path(), '', $path) . '/resources/views/partials/svgs.twig';
            $DummySvgSourcePath        = '.' . str_replace(base_path(), '', $path) . '/resources/assets/svgs/*.svg';

            //$filesystem->makeDirectory($resourcesPath . $dir);
            // Get webpack.mix.js stub
            $webpack    = $filesystem->get($avviarePath . '/resources/stubs/webpack.mix.js');

            $webpack    = str_replace('DummyAppJS', $jsPath, $webpack);
            $webpack    = str_replace('DummyAppCSS', $cssPath, $webpack);
            $webpack    = str_replace('DummySvgSpriteDestination', $DummySvgSpriteDestination, $webpack);
            $webpack    = str_replace('DummySvgSourcePath', $DummySvgSourcePath, $webpack);
            //
            // /Users/williamastrom/Sites/pyrotheme
            $filesystem->put(base_path('webpack.mix.js'), $webpack);
        }

        if ($this->confirm('Would you like to replace the existing package.json file with the current one used by laravel?')) {
            // Delete webpack
            $filesystem->delete(base_path('package.json'));
            // Copy webpack
            //$filesystem->copy($avviarePath . '/resources/stubs/package.json', base_path('package.json'));
            $file = file_get_contents($this->packageJsonUrl);
            $filesystem->put(base_path('package.json'), $file);
        }

        $this->comment("That's it, you are ready to start developing!");
    }
}
