<?php
/**
 * Created by PhpStorm.
 * User: ysn
 * Date: 17/07/2019
 * Time: 09:22
 * This class define a possible path in the workflow
 */

namespace Ysn\LaravelMultiWorkflows\Workflows;


use InvalidArgumentException;

class Pipeline
{
    private $name;
    private $steps;

    /**
     * Pipeline constructor.
     * @param $name
     * @param $steps
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->steps = [];
    }

    /**
     * Add one step
     * @param Step $step
     */
    public function addStep(Step $step)
    {
        if ($step instanceof Step){
            array_push($this->steps,$step);
        }else{
            throw new InvalidArgumentException("argument is not a Step object");
        }

    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * get all steps
     * @return array
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * Return a Step by name
     * @param $stepName
     * @return Step
     */
    public function getStep($stepName):Step
    {
        $step = array_filter($this->getSteps(),function ($e) use ($stepName)
        {
            return $e->getName() == $stepName;
        });
        return $step[0];

    }

    public function getDefaultStep():Step
    {
        $step = array_filter($this->getSteps(),function ($e)
        {
            return $e->isDefault() == true;
        });
        return $step[0];
    }

    /**
     * @param array $steps
     * @throws InvalidArgumentException
     */
    public function setSteps(array $steps): void
    {
        foreach ($steps as $step) {
            if(!$step instanceof Step){
                throw new \InvalidArgumentException("argument is not an array of Step instance");
            }
        }
        $this->steps = $steps;
    }


}