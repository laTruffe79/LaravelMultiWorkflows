<?php
/**
 * Created by PhpStorm.
 * User: ysn
 * Date: 05/07/2019
 * Time: 19:53
 */

namespace Ysn\LaravelMultiWorkflows\Workflows;

use Illuminate\Support\Facades\Event;
use Ysn\LaravelMultiWorkflows\Exceptions\TransitionConditionDenied;
use InvalidArgumentException;
use Ysn\LaravelMultiWorkflows\Models\Workflow;

abstract class BaseWorkflow implements WorkflowInterface
{

    /**
     * The definition var describes the different workflow's paths that an extended BaseWorkflow class can have
     * La variable definition est la définition des différents workflows que peut avoir une classe qui hérite de  BaseWorkflow
     * Example :
     * WorflowEtablissement
     *      pipeline :
     *          [
     *              steps :
     *              [
     *                  'notValidated' :
     *                  [
     *                      'stepName' : 'notValidated',
     *                      'stepColor' : 'red'
     *                      transitions :
     *                      [
     *                          'validation' :
     *                          [
     *                              'transitionName' : string,
     *                              'transitionCondition' => {code},// true or false
     *                              'stepDestination' => 'validated',
     *                              'transitionEvent' : Event, // Event or null
     *                          ],
     *                          'notification' :
     *                          [
     *                              'transitionName' : string,
     *                              'transitionCondition' => {code},// true or false
     *                              'stepDestination' => 2,
     *                              'transitionEvent' : Event, //Event or null
     *                          ],
     *
     *                      ]
     *                  ],
     *                  'validated' :
     *                  [
     *                      'stepName' : 'Validé',
     *                      'stepColor' : 'green'
     *                      transitions :
     *                      [
     *                          'invalidation' :
     *                          [
     *                              'transitionName' : string,
     *                              'transitionCondition' => {code},// true or false
     *                              'stepDestination' => 'notValidated',
     *                              'transitionEvent' : Event,
     *                          ],
     *                          'notification' :
     *                          [
     *                              'transitionName' : string,
     *                              'transitionCondition' => {code},// true or false
     *                              'stepDestination' => 'notified,
     *                              'transitionEvent' : Event,
     *                          ],
     *
     *                      ]
     *                  ]
     *              ]
     *          ]
     * @var definition
     */

    private $pipelines;

    /**
     * The array of pipelines must have the Pipeline->name as key
     * BaseWorkflow constructor.
     * @param array $pipelines
     * @throws InvalidArgumentException
     */
    public function __construct(array $pipelines)
    {
        //dd($pipelines);

        foreach ($pipelines as $pipeline) {
            if (!$pipeline instanceof Pipeline) {
                throw new InvalidArgumentException("pipeline array element is not a Pipeline object");
            }
            //dd($pipeline);
        }
        //dd($pipelines);
        $this->pipelines = $pipelines;
        //return $this;
        //dd($this->pipelines);
    }

    /**
     * @param Pipeline $pipelineName
     * @return Pipeline
     */
    public function getPipeline($pipelineName): Pipeline
    {
        $pipeline = array_filter($this->pipelines, function ($e) use ($pipelineName) {
            return $e->getName() == $pipelineName;
        });
        return $pipeline[0];
    }

    public function addPipeline(Pipeline $pipeline)
    {
        $this->pipelines[$pipeline->getName()] = $pipeline;
    }

    /**
     * Return all the steps of a Pipeline
     * @param $pipelineName
     * @return array
     */
    public function getSteps($pipelineName): array
    {
        return $this->pipelines[$pipelineName]->getSteps();
    }

    /**
     * Apply a transition of a specified step in a specified pipeline
     * @param string $transitionName
     * @param $workflowClass
     * @param $model
     * @param $modelId
     * @param $pipelineName
     * @throws TransitionConditionDenied
     * @throws \Ysn\LaravelMultiWorkflows\Exceptions\TransitionNotFoundException
     */
    public function apply(string $transitionName,string $workflowClass,string $model,int $modelId,string $pipelineName): void
    {
        $currentStep = $this->getCurrentStep($workflowClass, $model, $modelId, $pipelineName);

        $transition = $currentStep->getTransition($transitionName);


        if ($currentStep->getTransition($transitionName)->isCondition()) {

            //update or create a Workflow model in database
            Workflow::updateOrCreate(
                [ 'workflow_class' => $workflowClass,
                    'workflowable_type' => $model,
                    'workflowable_id' => $modelId,
                    'pipeline_name' => $pipelineName],
                [
                    'current_step' =>$transition->getStepDestination()
                ]
            );
            // call the associated event
            if ($transition->getTransitionEvent() !== null)
                event($transition->getTransitionEvent());
        } else {
            throw new TransitionConditionDenied($transition);
        }
    }

    /**
     * Get the current step or create it with de default step as current_step
     * @param $workflowClass string
     * @param $model string
     * @param $modelId int
     * @param $pipelineName string
     * @return Step
     */
    public function getCurrentStep($workflowClass, $model, $modelId, $pipelineName): Step
    {

        $workflow = Workflow::firstOrCreate([
            'workflow_class' => $workflowClass,
            'workflowable_type' => $model,
            'workflowable_id' => $modelId,
            'pipeline_name' => $pipelineName
        ],
            [
                'workflow_class' => $workflowClass,
                'workflowable_type' => $model,
                'workflowable_id' => $modelId,
                'pipeline_name' => $pipelineName,
                'current_step' => $this->getPipeline($pipelineName)->getDefaultStep()->getName()
            ]
        );

        return $this->getPipeline($workflow->pipeline_name)->getStep($workflow->current_step);

    }


}