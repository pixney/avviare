<?php

namespace Pixney\AvviareExtension\Command;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Pixney\AvviareExtension\AvviareExtension;
use Anomaly\Streams\Platform\Application\Application;
use Anomaly\Streams\Platform\Addon\Console\Command\MakeAddonPaths;

class Create extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avviare:create {theme} {--shared=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description    = 'Scaffold theme';

    /**
     * Theme namespace
     *
     * @var string
     */
    protected $namespace      = '';

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
     * Undocumented variable
     *
     * @var array
     */
    protected $wantedDirectories = [
        'sass',
        'js',
        'views'
    ];

    /**
     * Path to our installed extension
     *
     * @var string
     */
    protected $extPath;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->filesystem = app(Filesystem::class);
        $this->extPath= app(AvviareExtension)->path;
        $this->path = 
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Application $application, AvviareExtension $ext)
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

        Artisan::call('make:addon', [
            'namespace' => $this->namespace
        ]);

        // Delete directories
        foreach ($this->unwantedDirectories as $dir) {
            $this->filesystem->deleteDirectory($resourcesPath . $dir);
            $this->info('Deleted: ' . $resourcesPath . $dir);
        }

        // Delete Files
        foreach ($this->unwantedFiles as $file) {
            $this->filesystem->delete($path . $file);
            $this->info('Deleted: ' . $path . $file);
        }

        // Create new directories
        foreach ($this->wantedDirectories as $dir) {
            $this->filesystem->makeDirectory($resourcesPath . $dir);
            $this->info('Created: ' . $resourcesPath . $dir);
        }

        // Copy JS files
        $this->filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/js',
            "{$path}/resources/js"
        );
        $this->info('Javascript files copied');

        // Copy SCSS files
        $this->filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/sass',
            "{$path}/resources/sass"
        );
        $this->info('Sass files copied');

        // Copy VIEWS files
        $this->filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/views',
            "{$path}/resources/views"
        );

        // Copy Svg files
        $this->filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/svgs',
            "{$path}/resources/assets/svgs"
        );

        // Copy Image files
        $this->filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/images',
            "{$path}/resources/images"
        );

        // Copy Command files
        $this->filesystem->copyDirectory(
            $avviarePath . '/resources/stubs/Command',
            "{$path}/src/Command"
        );

        // Copy package.json
        $packagejson    = $this->filesystem->get($avviarePath . '/resources/stubs/package.json');
        $this->filesystem->put(base_path('package.json'), $packagejson);

        if ($this->confirm('Would you like us to automatically set your webpack.mix.js file?')) {
            $jsPath                    = '.' . str_replace(base_path(), '', $path) . '/resources/js/app.js';
            $cssPath                   = '.' . str_replace(base_path(), '', $path) . '/resources/sass/theme.scss';
            $DummySvgSpriteDestination = '..' . str_replace(base_path(), '', $path) . '/resources/views/partials/svgs.twig';
            $DummySvgSourcePath        = '.' . str_replace(base_path(), '', $path) . '/resources/assets/svgs/*.svg';

            //$this->filesystem->makeDirectory($resourcesPath . $dir);
            // Get webpack.mix.js stub
            $webpack    = $this->filesystem->get($avviarePath . '/resources/stubs/webpack.mix.js');

            $webpack    = str_replace('DummyAppJS', $jsPath, $webpack);
            $webpack    = str_replace('DummyAppCSS', $cssPath, $webpack);
            $webpack    = str_replace('DummySvgSpriteDestination', $DummySvgSpriteDestination, $webpack);
            $webpack    = str_replace('DummySvgSourcePath', $DummySvgSourcePath, $webpack);

            $this->filesystem->put(base_path('webpack.mix.js'), $webpack);
        }

        // if ($this->confirm('Would you like to replace the existing package.json file with the current one used by laravel?')) {
        //     // Delete webpack
        //     $this->filesystem->delete(base_path('package.json'));
        //     // Copy webpack
        //     //$this->filesystem->copy($avviarePath . '/resources/stubs/package.json', base_path('package.json'));
        //     $file = file_get_contents($this->packageJsonUrl);
        //     $this->filesystem->put(base_path('package.json'), $file);
        // }

        $this->comment('You need to run npm install and then you are ready to start development!');
    }
}
