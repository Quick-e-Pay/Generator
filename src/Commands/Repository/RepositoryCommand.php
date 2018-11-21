<?php

namespace Quick3Pay\Generator\Commands\Repository;

use Quick3Pay\Generator\Commands\CommandsInterface;
use Quick3Pay\Generator\Commands\GeneratorCommand;

class RepositoryCommand extends GeneratorCommand implements CommandsInterface
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffold:repository {model} {--filesystem}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new Repository Classes';

    protected $model;
    protected $modelName;

    protected $files = [
        'contracts'            => [
            'CrudInterface'                 => __DIR__ . '/stubs/Contracts/CrudInterface.stub',
            'EloquentRepositoryInterface'   => __DIR__ . '/stubs/Contracts/EloquentRepositoryInterface.stub',
            'FilesystemRepositoryInterface' => __DIR__ . '/stubs/Contracts/FilesystemRepositoryInterface.stub',
            'RepositoryInterface'           => __DIR__ . '/stubs/Contracts/RepositoryInterface.stub',
        ],
        'abstractRepositories' => [
            'EloquentRepository' => __DIR__ . '/stubs/Repositories/Eloquent/EloquentRepository.stub',
        ],
        'repositories'         => [
            'Contracts'  => __DIR__ . '/stubs/Repositories/Contracts/Interface.stub',
            'Eloquent'   => __DIR__ . '/stubs/Repositories/Eloquent/Repository.stub',
            'Filesystem' => __DIR__ . '/stubs/Repositories/Filesystem/Repository.stub',
        ],
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->checkModel();

        $this->createContracts();
        $this->createAbstractRepositories();
        $this->createModelInterface();
        $this->createModelRepository();

        $this->info("Repository {$this->model} created!");
        $this->line("");
        $this->warn("Dont forget to add the interface bind in the RepositoryServiceProvider File:");
        $this->warn("\$this->app->bind({$this->modelName}Interface::class, {$this->modelName}Repository::class);");
    }

    protected function checkModel()
    {
        $model = $this->namespace . $this->argument('model');

        $this->model = str_replace('/', '\\', $model);

        if ($this->laravel->runningInConsole()) {
            if (!class_exists($this->model)) {
                $this->error("Class {$model} does not exist!");
                exit();


//                $response = $this->ask("Model [{$this->model}] does not exist. Would you like to create it?", 'Yes');
//                if ($this->isResponsePositive($response)) {
//                    Artisan::call('make:model', [
//                        'name' => $this->model,
//                    ]);
//                    $this->line("Model [{$this->model}] has been successfully created.");
//                } else {
//                    $this->line("Model [{$this->model}] is not being created.");
//                }
            }
        }
        $modelParts = explode('\\', $this->model);
        $this->modelName = array_pop($modelParts);
    }

    protected function createContracts()
    {
        foreach ($this->files['contracts'] as $key => $value) {

            $folderPath = app()->basePath() . '/app/Contracts/Repositories/';
            $filePath = $folderPath . $key . '.php';

            $replacements = [
                '%namespaces.contracts%' => $this->namespace . 'Contracts\Repositories',

            ];

            $this->createFile($value, $replacements, $filePath, $folderPath);
        }
    }

    protected function createAbstractRepositories()
    {
        $contract = $this->files['abstractRepositories']['EloquentRepository'];

        $folderPath = app()->basePath() . '/app/Repositories/Eloquent/';
        $filePath = $folderPath . 'EloquentRepository.php';

        $replacements = [
            '%namespaces.contracts%' => $this->namespace . 'Repositories\Eloquent',
        ];

        $this->createFile($contract, $replacements, $filePath, $folderPath);

    }

    public function createModelInterface()
    {
        $contract = $this->files['repositories']['Contracts'];

        $folderPath = app()->basePath() . '/app/Repositories/Contracts/';
        $filePath = $folderPath . $this->modelName . 'Interface.php';

        $replacements = [
            '%namespaces.contracts%' => $this->namespace . 'Repositories\Contracts',
            '%modelName%'            => $this->modelName,
            '%use.interface%'        => $this->namespace . 'Contracts\Repositories\CrudInterface',
        ];

        $this->createFile($contract, $replacements, $filePath, $folderPath);
    }

    protected function createModelRepository()
    {
        $stubKey = $this->option('filesystem') ? 'Filesystem' : 'Eloquent';

        $contract = $this->option('filesystem') ? $this->files['repositories'][$stubKey] : $this->files['repositories'][$stubKey];

        $folderPath = app()->basePath() . '/app/Repositories/' . $stubKey . '/';
        $filePath = $folderPath . $this->modelName . 'Repository.php';

        $replacements = [
            '%namespaces.contracts%' => $this->namespace . 'Repositories\\' . $stubKey,
            '%use.model%'            => $this->model,
            '%extends%'              => $stubKey == 'Eloquent' ? 'extends EloquentRepository ' : '',
            '%use.modelName%'        => $this->modelName,
            '%use.modellowname%'     => strtolower($this->modelName),
            '%use.interface%'        => $this->namespace . 'Repositories\Contracts\\' . $this->modelName . 'Interface',
        ];

        $this->createFile($contract, $replacements, $filePath, $folderPath);
    }
}
