<?php

namespace CrudGenerator;
use Illuminate\Support\ServiceProvider;

use CrudGenerator\Commands\CrudGenerator;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $path = app_path("/Console/Commands");
        if (!File::exists($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Services Directory "%s" was not created', $path));
            }
        }
        $this->publishes([
            __DIR__.'/Commands/CrudGenerator.php' => $path,
        ]);
    }

}
