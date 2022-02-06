<?php

namespace Avxman\MediaLibraryExt\Providers;

use Avxman\MediaLibraryExt\Classes\MediaLibraryExtClass;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class MediaLibraryExtServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @param Filesystem $filesystem
     * @return void info
     */
    public function boot(Filesystem $filesystem){
        if(App()->runningInConsole()){
            $this->publishes($this->getFilesNameAll($filesystem, 0, true), 'avxman-media-library-ext-all');
            $this->publishes($this->getFilesNameAll($filesystem), 'avxman-media-library-ext-config');
            $this->publishes($this->getFilesNameAll($filesystem, 1), 'avxman-media-library-ext-views');
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(self::class, MediaLibraryExtClass::class);
    }

    /**
     * Create specified files in folders
     * @param Filesystem $filesystem
     * @param int $index
     * @param bool $all
     * @return array
     */
    protected function getFilesNameAll(Filesystem $filesystem, int $index = 0, bool $all = false) : array{
        $collect = collect()->push(
            [
                dirname(__DIR__, 2).'/config/' => base_path('config').DIRECTORY_SEPARATOR,
            ],
            [
                dirname(__DIR__, 2).'/views/' => resource_path('views').DIRECTORY_SEPARATOR,
            ]
        );
        return $all
            ? collect()->merge($collect->get(0))->merge($collect->get(1))->toArray()
            : $collect->get($index);
    }


}
