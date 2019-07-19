<?php
/**
 * Created by PhpStorm.
 * User: ysn
 * Date: 17/07/2019
 * Time: 10:53
 */

namespace Ysn\LaravelMultiWorkflows\Exceptions;

use Ysn\LaravelMultiWorkflows\Workflows\Transition;

class TransitionConditionDenied extends \Exception
{
    private $transition;

    /**
     * TransitionConditionDenied constructor.
     * @param $transition
     */
    public function __construct(Transition $transition)
    {
        parent::__construct();
        $this->transition = $transition;
    }


    public function report()
    {
        Log::error('Error transition "'.$this->transition->getName().'" not OK');
    }

    public function render()
    {
        return session()->flash('error_message', 'La condition de la transition '.$this->transition->getName().': "'.$this->transition->getConditionDescription().'" n\'est pas remplie');
    }

}