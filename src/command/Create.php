<?php

namespace Pixney\AvviareExtension\Command;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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
     * Package json file to download
     *
     * @var [type]
     */
    protected $packageJsonUrl='https://raw.githubusercontent.com/laravel/laravel/v5.8.16/package.json';

    /**
     * Various types of scaffolding options.
     *
     * @var array
     */
    protected $scaffoldingTypes=['Barebone', 'Tailwind', 'Editorial'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->filesystem        = app(Filesystem::class);
        $this->extPath           = app(AvviareExtension::class)->path;
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

        if ($type !== 'theme') {
            throw new \Exception('The type has to be theme.');
        }

        $themePath                 = $this->dispatch(new MakeAddonPaths($vendor, $type, $slug, $this));
        $type                      = str_singular($type);
        $themeResourcesPath        = $themePath . '/resources/';

        $this->call('make:addon', [
            'namespace' => $this->namespace
        ]);

        // Delete unwanted directories
        foreach ($this->unwantedDirectories as $dir) {
            $this->filesystem->deleteDirectory($themeResourcesPath . $dir);
            $this->info('Deleted: ' . $themeResourcesPath . $dir);
        }

        // Delete unwanted Files
        foreach ($this->unwantedFiles as $file) {
            $this->filesystem->delete($themePath . $file);
            $this->info('Deleted: ' . $themePath . $file);
        }

        $chosenScaffoldType = strtolower($this->choice('Choose theme?', $this->scaffoldingTypes, 0));

        $from = $this->extPath . "/resources/stubs/themes/{$chosenScaffoldType}/resources";
        $to   = "{$themePath}/resources";

        $this->filesystem->copyDirectory($from, $to);

        if ($chosenScaffoldType === 'barebone') {
            // $this->filesystem->delete(base_path('package.json'));
            // $file = file_get_contents($this->packageJsonUrl);
            // $this->filesystem->put(base_path('package.json'), $file);
            $packagejson    = $this->filesystem->get($this->extPath . "/resources/stubs/{$chosenScaffoldType}/package.json");
            $this->filesystem->put(base_path('package.json'), $packagejson);

            if ($this->confirm('Would you like us to automatically set your webpack.mix.js file?')) {
                $jsPath                    = '.' . str_replace(base_path(), '', $themePath) . '/resources/js/app.js';
                $cssPath                   = '.' . str_replace(base_path(), '', $themePath) . '/resources/sass/theme.scss';
                $DummySvgSpriteDestination = '..' . str_replace(base_path(), '', $themePath) . '/resources/views/partials/svgs.twig';
                $DummySvgSourcePath        = '.' . str_replace(base_path(), '', $themePath) . '/resources/assets/svgs/*.svg';

                //$this->filesystem->makeDirectory($themeResourcesPath . $dir);
                // Get webpack.mix.js stub
                $webpack    = $this->filesystem->get($this->extPath . "/resources/stubs/themes/{$chosenScaffoldType}/webpack.mix.js");

                $webpack    = str_replace('DummyAppJS', $jsPath, $webpack);
                $webpack    = str_replace('DummyAppCSS', $cssPath, $webpack);
                $webpack    = str_replace('DummySvgSpriteDestination', $DummySvgSpriteDestination, $webpack);
                $webpack    = str_replace('DummySvgSourcePath', $DummySvgSourcePath, $webpack);

                $this->filesystem->put(base_path('webpack.mix.js'), $webpack);
            }
        }

        if ($chosenScaffoldType === 'tailwind') {
            $packagejson    = $this->filesystem->get($this->extPath . "/resources/stubs/{$chosenScaffoldType}/package.json");
            $this->filesystem->put(base_path('package.json'), $packagejson);

            if ($this->confirm('Would you like us to automatically set your webpack.mix.js file?')) {
                // Set path variables
                $pathToScssFile     = '.' . str_replace(base_path(), '', $themePath) . '/resources/sass/theme.scss';
                $pathToTailwindConf = '.' . str_replace(base_path(), '', $themePath) . '/resources/tailwind.config.js';

                // Get webpack.mix.js stub
                $webpack    = $this->filesystem->get($this->extPath . "/resources/stubs/themes/{$chosenScaffoldType}/webpack.mix.js");
                $webpack    = str_replace('DummyAppCSS', $pathToScssFile, $webpack);
                $webpack    = str_replace('DummyTailwindConfPath', $pathToTailwindConf, $webpack);
                $this->filesystem->put(base_path('webpack.mix.js'), $webpack);

                //     $jsPath                    = '.' . str_replace(base_path(), '', $themePath) . '/resources/js';
                //     $webpack    = str_replace('DummyAppJS', $jsPath, $webpack);
            }
        }

        // Copy Command files
        // $this->filesystem->copyDirectory(
        //     $this->extPath . '/resources/stubs/Command',
        //     "{$themePath}/src/Command"
        // );

        //$packagejson    = $this->filesystem->get($this->extPath . '/resources/stubs/package.json');
        //$this->filesystem->put(base_path('package.json'), $packagejson);

        // if ($this->confirm('Would you like us to automatically set your webpack.mix.js file?')) {
        //     $jsPath                    = '.' . str_replace(base_path(), '', $themePath) . '/resources/js/app.js';
        //     $cssPath                   = '.' . str_replace(base_path(), '', $themePath) . '/resources/sass/theme.scss';
        //     $DummySvgSpriteDestination = '..' . str_replace(base_path(), '', $themePath) . '/resources/views/partials/svgs.twig';
        //     $DummySvgSourcePath        = '.' . str_replace(base_path(), '', $themePath) . '/resources/assets/svgs/*.svg';

        //     //$this->filesystem->makeDirectory($themeResourcesPath . $dir);
        //     // Get webpack.mix.js stub
        //     $webpack    = $this->filesystem->get($this->extPath . "/resources/stubs/{$chosenScaffoldType}/webpack.mix.js");

        //     $webpack    = str_replace('DummyAppJS', $jsPath, $webpack);
        //     $webpack    = str_replace('DummyAppCSS', $cssPath, $webpack);
        //     $webpack    = str_replace('DummySvgSpriteDestination', $DummySvgSpriteDestination, $webpack);
        //     $webpack    = str_replace('DummySvgSourcePath', $DummySvgSourcePath, $webpack);

        //     $this->filesystem->put(base_path('webpack.mix.js'), $webpack);
        // }

        // if ($this->confirm('Would you like to replace the existing package.json file with the current one used by laravel?')) {
        //     // Delete webpack
        //     $this->filesystem->delete(base_path('package.json'));
        //     // Copy webpack
        //     //$this->filesystem->copy($this->extPath . '/resources/stubs/package.json', base_path('package.json'));
        //     $file = file_get_contents($this->packageJsonUrl);
        //     $this->filesystem->put(base_path('package.json'), $file);
        // }

        $this->comment('Now all you need to do is run : npm install');
    }
}
