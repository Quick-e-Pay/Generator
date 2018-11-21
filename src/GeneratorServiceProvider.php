<?php
/**
 * Criado por Maizer Aly de O. Gomes para api.quickepay.
 * Email: maizer.gomes@gmail.com / maizer.gomes@ekutivasolutions / maizer.gomes@outlook.com
 * Usuário: maizerg
 * Data: 11/21/18
 * Hora: 12:45 PM
 */

namespace Quick3Pay\Generator;


use Illuminate\Support\ServiceProvider;
use Quick3Pay\Generator\Commands\Repository\RepositoryCommand;

class GeneratorServiceProvider extends ServiceProvider
{

    /**
     * Commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        RepositoryCommand::class,
    ];


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->mergeConfigFrom(__DIR__.'/config/repoist.php', 'repoist');
//        $this->publishes([
//            __DIR__.'/config/repoist.php' => app()->basePath() . '/config/repoist.php'
//        ], 'repoist-config');
        $this->registerCommands();
    }

    public function registerCommands()
    {
        $this->commands($this->commands);
    }
}