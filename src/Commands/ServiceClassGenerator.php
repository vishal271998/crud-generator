<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ServiceClassGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate service class';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getStub($type)
    {
        return file_get_contents("../stubs/$type.stub");
    }

    protected function service($model){
        $classname = $model.'Service';

        $serviceTemplate = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ model }}',
                '{{ modelNamespace }}',
            ],
            [
                'App\Services',
                $classname,
                $model,
                'App\Models',
            ],
            $this->getStub('service')
        );

        $path = app_path("/Services");
        if (!File::exists($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Services Directory "%s" was not created', $path));
            }
        }

        file_put_contents(app_path("/Services/{$classname}.php"), $serviceTemplate);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = $this->argument('model');

        $this->service($model);
    }

}
