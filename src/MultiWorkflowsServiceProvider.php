<?php

namespace Ysn\LaravelMultiWorkflows;

use Illuminate\Support\ServiceProvider;

class MultiWorkflowsServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        //publish migrations and model
        $this->publishes([
            __DIR__.'/database/migrations/2019_07_18_142805_create_workflows_table.php' => 'create_workflows_table.php'
        ],'migrations');

    }

    public function register()
    {
        $this->app->bind('Ysn\LaravelMultiWorkflows\Workflows\WorkflowInterface','Ysn\LaravelMultiWorkflows\Workflows\BaseWorkflow');
    }


}