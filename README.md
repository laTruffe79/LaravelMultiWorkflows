# Multi Workflows Laravel Package

This package allows you to define multiple workflows associated to a Model.
Every workflows can have multiple Pipelines > Steps > Transitions

#Installation

first install package with composer
```bash
composer install ysn/LaravelMultiWorkflows
```
next publish vendors to get migration
```bash
php artisan vendor:publish
```
finally execute the migration
```bash
php artisan migrate
```

#Usage

First create a Workflow class related to your Model to define your Workflow like that
```php
<?php
/**
 * Created by PhpStorm.
 * User: ysn
 * Date: 05/07/2019
 * Time: 20:02
 */

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;
use Ysn\LaravelMultiWorkflows\Workflows\BaseWorkflow;
use Ysn\LaravelMultiWorkflows\Workflows\Pipeline;
use Ysn\LaravelMultiWorkflows\Workflows\Step;
use Ysn\LaravelMultiWorkflows\Workflows\Transition;

class EtablissementWorkflow extends BaseWorkflow
{

    private static $workflowClass;

    private static function defaultPipeline()
    {
        return array(new Pipeline('validationEtablissement'));
    }


    public function __construct($newArrayPipelines)
    {
        self::$workflowClass = get_class($this);

        // We create a pipeline
        if(empty($newArrayPipelines)){
            $newArrayPipelines = self::defaultPipeline();
        }

        // We add it to the workflow
        parent::__construct($newArrayPipelines);

        // We create the steps
        $stepNotValidated = new Step('notValidated','red','non validé',true);
        $stepValidated = new Step('validated','green','validé');
        // put the steps in an array
        $steps = array(
            $stepNotValidated,
            $stepValidated,
        );

        // We pass the array to the Pipeline class addSteps method
        $validationPipeline = parent::getPipeline('validationEtablissement');
        $validationPipeline->setSteps($steps);

        // we set the transitions
        $user = Auth::getUser();
        $transitionValidate = new Transition(
            'validate',
            $user->hasPermissionTo('validate_etablissement'),
            'Avoir le droit de valider un établissement',
            'validated',
            'EtablissementValidatedEvent');

        $transitionUnvalidate = new Transition(
            'unvalidate',
            true,
            'pas de condition particulière',
            'notValidated',
            'EtablissementUnvalid');

        //We add the transitions to the steps
        $stepNotValidated->addTransition($transitionValidate);
        $stepValidated->addTransition($transitionUnvalidate);

    }

    /**
     * @return string
     */
    public static function getWorkflowClass(): string
    {
        return self::$workflowClass;
    }

}
```
Create the events associated to your transitions.

Now you can use your Workflow class in a controller

```php
    // We call the workflow
    $instanceWorkflow = new EtablissementWorkflow(array());
    $pipeline = $instanceWorkflow->getPipeline('validationEtablissement');

    //get current step
    $currentStep = $instanceWorkflow->getCurrentStep(
        $instanceWorkflow::getWorkflowClass(),
        'App\Models\Etablissement',
        $etablissement->id,
        'validationEtablissement'
    );
    
    // apply a transition
    $instanceWorkflow->apply(string $transitionName,string $workflowClass,string $model,int $modelId,string $pipelineName);
```
 