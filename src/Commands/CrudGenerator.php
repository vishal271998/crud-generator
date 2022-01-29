<?php

namespace Vishal\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
//    protected $signature = 'command:name';
    protected $signature = 'crud:generator
    {name : Class (singular)} {constraints?*}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';


    protected $modelNamespace;
    protected $controllerNamespace;
    protected $requestNamespace;
    protected $policyNamespace;
    protected $mailNamespace;
    protected $importNamespace;
    protected $exportNamespace;
    protected $userModel;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelNamespace = 'App\Models';
        $this->requestNamespace = 'App\Http\Requests';
        $this->controllerNamespace = 'App\Http\Controllers';
        $this->policyNamespace = 'App\Policies';
        $this->mailNamespace = 'App\Mail';
        $this->importNamespace = 'App\Imports';
        $this->exportNamespace = 'App\Exports';
        $this->userModel = 'User';
    }

    protected function getStub($type)
    {
        return file_get_contents("../stubs/$type.stub");
    }

    protected function model($model)
    {
        $filePath = app_path("Models/{$model}.php");

        if(file_exists($filePath)) {
            throw new \Exception("{$model} Model Already Exist");
        }

        $modelTemplate = str_replace(
            [
                '{{ model }}',
                '{{ modelNamespace }}',
            ],
            [
                $model,
                $this->modelNamespace
            ],
            $this->getStub('model')
        );

        file_put_contents($filePath, $modelTemplate);
    }

    protected function controller($model)
    {
        $class = $model.'Controller';

        $filePath = app_path("/Http/Controllers/{$class}.php");

        if(file_exists($filePath)) {
            throw new \Exception("{$class} Already Exist");
        }

        $fileName = $this->slug($model, '_');

        $controllerTemplate = str_replace(
            [
                '{{ model }}',
                '{{ modelNamePluralLowerCase }}',
                '{{ modelVariable }}',
                '{{ modelNamespace }}',
                '{{ requestNamespace }}',
                '{{ namespace }}',
                '{{ fileName }}',
                '{{ class }}'
            ],
            [
                $model,
                lcfirst(Str::plural($model)),
                lcfirst($model),
                $this->modelNamespace,
                $this->requestNamespace,
                $this->controllerNamespace,
                $fileName,
                $class
            ],
            $this->getStub('controller')
        );

        file_put_contents($filePath, $controllerTemplate);
    }

    protected function request($model)
    {
        $class = "{$model}Request";

        $dirPath = app_path("/Http/Requests");
        if (!File::exists($dirPath)){
            if (!mkdir($dirPath, 0777, true) && !is_dir($dirPath)) {
                throw new \RuntimeException(sprintf('Request Directory "%s" was not created', $dirPath));
            }
        }

        $filePath = app_path("/Http/Requests/$class.php");

        if(file_exists($filePath)) {
            throw new \Exception("{$class} Already Exist");
        }

        $requestTemplate = str_replace(
            [
                '{{ class }}',
                '{{ namespace }}'
            ],
            [
                $class,
                $this->requestNamespace
            ],
            $this->getStub('request')
        );



        file_put_contents($filePath, $requestTemplate);
    }

    protected function views($model)
    {
        $folderName = $this->slug($model, '_');
        $dirPath = resource_path("/views/{$folderName}");
        if (!File::exists($dirPath)) {
            if (!mkdir($dirPath, 0777, true) && !is_dir($dirPath)) {
                throw new \RuntimeException(sprintf('View Directory "%s" was not created', $dirPath));
            }
        }

        $indexFilePath = resource_path("views/{$folderName}/index.blade.php");
        if(file_exists($indexFilePath)) {
            throw new \Exception("{$folderName}/index view Already Exist");
        }

        $createFilePath = resource_path("views/{$folderName}/create.blade.php");
        if(file_exists($createFilePath)) {
            throw new \Exception("{{$folderName}/create view Already Exist");
        }

        $editFilePath = resource_path("views/{$folderName}/edit.blade.php");
        if(file_exists($editFilePath)) {
            throw new \Exception("{{$folderName}/edit view Already Exist");
        }


        $indexTemplate = str_replace(
            [
                '{{ model }}',
                '{{ modelNamePluralLowerCase }}',
                '{{ modelNameSingularLowerCase }}'
            ],
            [
                $model,
                lcfirst(Str::plural($model)),
                lcfirst($model)
            ],
            $this->getStub('views/index')
        );

        $createTemplate = str_replace(
            [
                '{{ model }}',
                '{{ modelNamePluralLowerCase }}',
                '{{ modelNameSingularLowerCase }}'
            ],
            [
                $model,
                lcfirst(Str::plural($model)),
                lcfirst($model)
            ],
            $this->getStub('views/create')
        );

        $editTemplate = str_replace(
            [
                '{{ model }}',
                '{{ modelNamePluralLowerCase }}',
                '{{ modelNameSingularLowerCase }}'
            ],
            [
                $model,
                lcfirst(Str::plural($model)),
                lcfirst($model)
            ],
            $this->getStub('views/edit')
        );

        file_put_contents($indexFilePath, $indexTemplate);
        file_put_contents($createFilePath, $createTemplate);
        file_put_contents($editFilePath, $editTemplate);
    }

    protected function migration($model)
    {
        $fileName = $this->slug($model, '_', 'plural');

        $controllerTemplate = str_replace(
            [
                '{{ model }}',
                '{{ modelNamePluralLowerCase }}'
            ],
            [
                str::plural($model),
                $fileName
            ],
            $this->getStub('migration')
        );

        $migrationName = date('Y').'_'.date('m').'_'.date('d').'_'.random_int(1000, 9999)."_create_{$fileName}_table.php";

        file_put_contents(database_path("/migrations/{$migrationName}"), $controllerTemplate);
    }

    protected function policy($model)
    {
        $className = $model.'Policy';

        $path = app_path("/Policies");
        if (!File::exists($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Policy Directory "%s" was not created', $path));
            }
        }

        $filePath = app_path("/Policies/{$className}.php");
        if(file_exists($filePath)) {
            throw new \Exception("{$classname} Already Exist");
        }

        $policyTemplate = str_replace(
            [
                '{{ model }}',
                '{{ modelVariable }}',
                '{{ class }}',
                '{{ namespace }}',
                '{{ namespaceModel }}',
                '{{ userModel }}',
            ],
            [
                $model,
                lcfirst($model),
                $className,
                $this->policyNamespace,
                $this->modelNamespace,
                $this->userModel,
            ],
            $this->getStub('policy')
        );

        file_put_contents($filePath, $policyTemplate);
    }

    protected function routes($model){

        $controller = $model.'Controller';
        $model = lcfirst($model);

        $routePrefix = $this->slug($model, '-', 'plural');
        $routeName = $this->slug($model, '_', 'plural');

        $routes = "
Route::group(['prefix' => '{$routePrefix}'], function () {
    Route::get('/', '{$controller}@index')->name('{$routeName}.index');
    Route::get('/create', '{$controller}@create')->name('{$routeName}.create');
    Route::post('/', '{$controller}@store')->name('{$routeName}.store');
    Route::get('/{{$model}}/show', '{$controller}@show')->name('{$routeName}.show');
    Route::get('/{{$model}}/edit', '{$controller}@edit')->name('{$routeName}.edit');
    Route::post('/{{$model}}/update', '{$controller}@update')->name('{$routeName}.update');
    Route::post('/{{$model}}/destroy', '{$controller}@destroy')->name('{$routeName}.destroy');
});
        ";

        File::append(base_path('routes/web.php'), $routes);
    }

    protected function mail($model){
        $classname = $model.'Mail';

        $dirPath = app_path("/Mail");
        if (!File::exists($dirPath)) {
            if (!mkdir($dirPath, 0777, true) && !is_dir($dirPath)) {
                throw new \RuntimeException(sprintf('Mail Directory "%s" was not created', $dirPath));
            }
        }

        $filePath = app_path("/Mail/{$classname}.php");
        if(file_exists($filePath)) {
            throw new \Exception("{$classname} Already Exist");
        }

        $slug = $this->slug($model, '_');
        $policyTemplate = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ slug }}'

            ],
            [
                $this->mailNamespace,
                $classname,
                $slug,
            ],
            $this->getStub('mail')
        );

        file_put_contents($filePath, $policyTemplate);
    }

    protected function import($model){
        $classname = $model.'Import';

        $path = app_path("/Imports");
        if (!File::exists($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Imports Directory "%s" was not created', $path));
            }
        }

        $filePath = app_path("/Imports/{$classname}.php");
        if(file_exists($filePath)) {
            throw new \Exception("{$classname} Already Exist");
        }

        $policyTemplate = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
            ],
            [
                $this->importNamespace,
                $classname,
            ],
            $this->getStub('import')
        );



        file_put_contents($filePath, $policyTemplate);
    }

    protected function export($model){
        $classname = $model.'Export';

        $dirPath = app_path("/Exports");
        if (!File::exists($dirPath)) {
            if (!mkdir($dirPath, 0777, true) && !is_dir($dirPath)) {
                throw new \RuntimeException(sprintf('Exports Directory "%s" was not created', $dirPath));
            }
        }

        $filePath = app_path("/Exports/{$classname}.php");
        if(file_exists($filePath)) {
            throw new \Exception("{$classname} Already Exist");
        }

        $policyTemplate = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
            ],
            [
                $this->exportNamespace,
                $classname,
            ],
            $this->getStub('export')
        );

        file_put_contents($filePath, $policyTemplate);
    }

    protected function constraints($constraints, $modelName){

        foreach ($constraints as $constraint){

            switch ($constraint){
                case "Mail":
                case "mail":
                    $this->mail($modelName);
                    break;
                case "Import":
                case "import":
                    $this->import($modelName);
                    break;
                case "Export":
                case "export":
                    $this->export($modelName);
                    break;
            }

        }
    }

    protected function slug($model, $seperator, $type = NULL){
        $model = lcfirst($model);

        $pattern = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
        preg_match_all($pattern, $model, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ?
                strtolower($match) :
                lcfirst($match);
        }

        if($type == 'plural'){
            return str::plural(implode($seperator, $ret));
        }else{
            return implode($seperator, $ret);
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modelName = $this->argument('name');

        $constraints = $this->argument('constraints');

        $this->controller($modelName);
        $this->model($modelName);
        $this->request($modelName);
        $this->views($modelName);
        $this->migration($modelName);
        $this->policy($modelName);
        $this->routes($modelName);
        $this->constraints($constraints, $modelName);

    }
}
