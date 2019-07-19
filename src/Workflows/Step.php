<?php
/**
 * Created by PhpStorm.
 * User: ysn
 * Date: 17/07/2019
 * Time: 09:46
 */

namespace Ysn\LaravelMultiWorkflows\Workflows;


use InvalidArgumentException;
use Ysn\LaravelMultiWorkflows\Exceptions\TransitionConditionDenied;
use Ysn\LaravelMultiWorkflows\Exceptions\TransitionNotFoundException;

/**
 * Class Step
 * @package Ysn\LaravelMultiWorkflows\Workflows
 */
class Step
{

    private $name;
    private $default;
    private $description;
    private $color;
    private $transitions;

    /**
     * Step constructor.
     * @param $name string
     * @param $color string
     * @param $description string
     * @param bool $default
     */
    public function __construct(string $name,string $color, string $description,bool $default = false)
    {
        $this->name = $name;
        $this->color = $color;
        $this->description = $description;
        $this->default = $default;
        $this->transitions = [];
    }

    /**
     * Add a transition to a step
     * @param Transition $transition
     */
    public function addTransition(Transition $transition)
    {
        if ($transition instanceof Transition) {
            $this->transitions[$transition->getName()] = $transition;
        } else {
            throw new InvalidArgumentException("Argument is not a Transition object");
        }
    }

    /**
     * Apply a transition if the condition is true
     * @param $transitionName string
     * @throws TransitionConditionDenied
     */
    public function apply($transitionName)
    {
        if (array_key_exists($transitionName, $this->transitions)) {
            if ($this->transitions[$transitionName]->isCondition()){
                event($this->transitions[$transitionName]->getTransitionEvent());
            }else{
                throw new TransitionConditionDenied();
            }
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
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color): void
    {
        $this->color = $color;
    }

    /**
     * get all transitions
     * @return array
     */
    public function getTransitions(): array
    {
        return $this->transitions;
    }

    /**
     * Get Specified transition by Name
     * @param string $transitionName
     * @return Transition
     * @throws TransitionNotFoundException
     */
    public function getTransition(string $transitionName):Transition
    {
        $transition = array_filter($this->getTransitions(),function ($e)use($transitionName){
           return $e->getName() == $transitionName;
        });
        if(!$transition[0] instanceof Transition)
        {
            throw new TransitionNotFoundException($transitionName);
        }
        return $transition[0];
    }


    /**
     * @param array $transitions
     */
    public function setTransitions(array $transitions): void
    {
        $this->transitions = $transitions;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @param bool $default
     */
    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }


}
