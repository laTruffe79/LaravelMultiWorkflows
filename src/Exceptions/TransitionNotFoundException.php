<?php
/**
 * Created by PhpStorm.
 * User: ysn
 * Date: 19/07/2019
 * Time: 09:17
 */

namespace Ysn\LaravelMultiWorkflows\Exceptions;


class TransitionNotFoundException extends \Exception
{

    private $transitionName;

    /**
     * TransitionNotFoundException constructor.
     * @param string $transitionName
     *
     */
    public function __construct(string $transitionName)
    {
        parent::__construct();
        $this->transitionName = $transitionName;
    }


    public function report()
    {
        Log::error('Error transition "'.$this->transitionName.'" doesn\'t exist');
    }

    public function render()
    {
        return session()->flash('error_message', 'Transition "'.$this->transitionName.'" doesn\'t exist');
    }

}