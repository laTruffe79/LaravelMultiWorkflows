<?php
/**
 * Created by PhpStorm.
 * User: ysn
 * Date: 16/07/2019
 * Time: 15:25
 */

namespace Ysn\LaravelMultiWorkflows\Workflows;


interface WorkflowInterface
{

    /**
     * WorkflowInterface constructor.
     * @param array $pipelines
     */
    public function __construct(array $pipelines);

    /**
     * Add a pipeline
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function getPipeline(Pipeline $pipeline);

    /**
     * Add a pipeline
     * @param Pipeline $pipeline
     * @return mixed
     */
    public function addPipeline(Pipeline $pipeline);

    /**
     * Return all the steps of a workflow
     * @param $pipeline
     * @return array
     */
    public function getSteps($pipeline):array;

    /**
     * Apply a transition of a specified step in a specified pipeline
     * @param string $transitionName
     * @param $workflowClass
     * @param $model
     * @param $modelId
     * @param $pipelineName
     */
    public function apply(string $transitionName,string $workflowClass,string $model,int $modelId,string $pipelineName):void ;


}