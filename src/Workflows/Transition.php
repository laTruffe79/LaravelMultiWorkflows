<?php
/**
 * Created by PhpStorm.
 * User: ysn
 * Date: 17/07/2019
 * Time: 10:10
 */

namespace Ysn\LaravelMultiWorkflows\Workflows;

use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Transition
 * @package Ysn\LaravelMultiWorkflows\Workflows
 */
class Transition
{

    private $name;
    private $condition;
    private $conditionDescription;
    private $stepDestination;
    private $transitionEvent;

    /**
     * Transition constructor.
     * @param string $name
     * @param bool $condition
     * @param string $conditionDescription
     * @param string $stepDestination
     * @param string $transitionEvent
     */
    public function __construct(string $name,bool $condition,string $conditionDescription,string $stepDestination,string $transitionEvent)
    {
        $this->name = $name;
        $this->condition = $condition;
        $this->conditionDescription = $conditionDescription;

        $this->stepDestination = $stepDestination;
        $this->transitionEvent = $transitionEvent;
    }

    /**
     * @return string
     */
    public function getTransitionEvent(): string
    {
        return $this->transitionEvent;
    }

    /**
     * Return the result of the condition to pass the transition
     * @return bool
     */
    public function isCondition(): bool
    {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getStepDestination(): string
    {
        return $this->stepDestination;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @param bool $condition
     */
    public function setCondition(bool $condition): void
    {
        $this->condition = $condition;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $stepDestination
     */
    public function setStepDestination(string $stepDestination): void
    {
        $this->stepDestination = $stepDestination;
    }


    /**
     * @param string $transitionEvent
     */
    public function setTransitionEvent(string $transitionEvent): void
    {
        $this->transitionEvent = $transitionEvent;
    }

    /**
     * @return string
     */
    public function getConditionDescription(): string
    {
        return $this->conditionDescription;
    }

    /**
     * @param string $conditionDescription
     */
    public function setConditionDescription(string $conditionDescription): void
    {
        $this->conditionDescription = $conditionDescription;
    }




}