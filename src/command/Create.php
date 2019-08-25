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
    protected $scaffoldingTypes=['Bootstrap', 'Tailwind'];

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

        $chosenScaffoldType = strtolower($this->choice('Choose theme?', $this->scaffoldingTypes, 1));

        // Copy files
        dispatch_now(new CopyFiles($this->extPath, $themePath, $chosenScaffoldType));

        if ($this->confirm('Would you like to have your webpack.mix.js files setup?')) {
            switch ($chosenScaffoldType) {
                case 'bootstrap':
                    dispatch_now(new BootstrapWebpack($this->extPath, $themePath, $chosenScaffoldType));
                break;

                case 'tailwind':
                    dispatch_now(new TailwindWebpack($this->extPath, $themePath, $chosenScaffoldType));
                break;
            }
        }

        $this->comment('Now all you need to do is run : npm install');
    }
}
