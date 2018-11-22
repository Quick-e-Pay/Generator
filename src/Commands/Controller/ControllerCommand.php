<?php
/**
 * Criado por Maizer Aly de O. Gomes para api.quickepay.
 * Email: maizer.gomes@gmail.com / maizer.gomes@ekutivasolutions / maizer.gomes@outlook.com
 * Usuário: maizerg
 * Data: 11/21/18
 * Hora: 9:10 PM
 */

namespace Quick3Pay\Generator\Commands\Controller;


use Quick3Pay\Generator\Commands\CommandsInterface;
use Quick3Pay\Generator\Commands\GeneratorCommand;

class ControllerCommand extends GeneratorCommand implements CommandsInterface
{

    protected $signature = 'scaffold:controller {controller} {--api-version=} {--model=} {--resource=} {--request=}';

    protected $description = 'Create new Controller Class';

    protected $files = [
        'APIController' => __DIR__ . '/stubs/APIController.stub',
    ];

    protected $modelName;
    protected $controllerName;
    protected $controller;
    protected $apiVersion = 'v2';
    protected $namespacePath;
    protected $resourceName;
    protected $requestName;

    public function handle()
    {
        $this->checkController();
        $this->createController();

        $this->info("Controller {$this->controller} created!");
    }

    protected function checkController()
    {
        $this->apiVersion = $this->option('api-version') ?: $this->apiVersion;
        $this->controller = $this->nameSpaceClass('Http\Controllers\\' . $this->apiVersion . '\\' . $this->argument('controller'));


        if ($this->laravel->runningInConsole()) {
            if (class_exists($this->controller)) {
                $this->error("Class {$this->controller} already exists!");
                exit();
            }
        }
        $this->controllerName = $this->extractNameFromClass($this->controller);
        $this->modelName = $this->option('model') ? $this->extractNameFromClass($this->option('model')) : str_replace('Controller', '', $this->controllerName);
        $this->resourceName = $this->option('resource') ? $this->extractNameFromClass($this->option('resource')) : $this->modelName;
        $this->requestName = $this->option('request') ? $this->extractNameFromClass($this->option('request')) : $this->modelName . 'Request';

        if (count($path = explode('\\', $this->argument('controller'))) > 1) {
            array_pop($path);
            $this->namespacePath = implode('\\', $path) . '\\';
        }
    }

    protected function nameSpaceClass($unNameSpacedclass)
    {
        $namespaced = $this->namespace . $unNameSpacedclass;

        return str_replace('/', '\\', $namespaced);
    }

    protected function extractNameFromClass($NameSpacedClass)
    {
        $nameParts = explode('\\', $NameSpacedClass);

        return array_pop($nameParts);
    }

    protected function createController()
    {
        $contract = $this->files['APIController'];

        $folderPath = str_replace('\\', '/', app()->basePath() . "/app/Http/Controllers/{$this->apiVersion}/{$this->namespacePath}");
        $filePath = $folderPath . $this->controllerName . '.php';

        $replacements = [
            '%namespace.apicontroller%' => rtrim($this->namespace . "Http\Controllers\\{$this->apiVersion}\\{$this->namespacePath}", '\\'),
            '%namespace.controller%'    => $this->namespace,
            '%use.request%'             => $request = $this->namespace . "Http\Requests\\{$this->apiVersion}\\{$this->namespacePath}{$this->requestName}",
            '%use.resource%'            => $resource = $this->namespace . "Http\Resources\\{$this->apiVersion}\\{$this->namespacePath}{$this->resourceName}",
            '%use.collection%'          => $collection = $this->namespace . "Http\Resources\\{$this->apiVersion}\\{$this->namespacePath}{$this->resourceName}Collection",
            '%use.interface%'           => $interface = $this->namespace . "Repositories\Contracts\\{$this->modelName}Interface",
            '%model.name%'              => strtolower($this->modelName),
            '%controller.name%'         => $this->controllerName,
            '%interface.name%'          => $this->modelName . 'Interface',
            '%collection.name%'         => $this->resourceName . 'Collection',
            '%request.name%'            => $this->requestName,
            '%resource.name%'           => $this->resourceName,
        ];

        if (!class_exists($request)) {
            if ($this->confirm("A {$this->requestName} does not exist. Do you want to generate it?", true)) {
                $this->call('make:request', ['name' => "{$this->apiVersion}\\{$this->namespacePath}{$this->requestName}"]);
            }
        }

        if (!interface_exists($interface)) {
            if ($this->confirm("A {$this->modelName}Interface does not exist. Do you want to generate it?", true)) {
                $this->call('scaffold:repository', [
                    'model' => $this->option('model') ?: $this->modelName,
                ]);
            }
        }

        if (!class_exists($resource)) {
            if ($this->confirm("A {$this->resourceName}Resource does not exist. Do you want to generate it?", true)) {
                $this->call('make:resource', ['name' => "{$this->apiVersion}\\{$this->namespacePath}{$this->resourceName}"]);
            }
        }

        if (!class_exists($collection)) {
            if ($this->confirm("A {$this->resourceName}Collection does not exist. Do you want to generate it?", true)) {
                $this->call('make:resource', ['name' => "{$this->apiVersion}\\{$this->namespacePath}{$this->resourceName}"]);
            }
        }

        $this->createFile($contract, $replacements, $filePath, $folderPath);
    }

    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }
}