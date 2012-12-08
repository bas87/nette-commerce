<?php

use Nette\Diagnostics\Debugger,
    Nette\Application as NA;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class ErrorPresenter extends BasePresenter
{
    /**
     * @param BadRequestException $exception 
     */
    public function renderDefault($exception)
    {
        if ($this->isAjax()) {
            $this->payload->error = TRUE;
            $this->terminate();
        } elseif ($exception instanceof NA\BadRequestException) {
            $code = $exception->getCode();
            $this->setView(in_array($code, array(403, 404, 405, 410, 500)) ? $code : '4xx');
            Debugger::log("HTTP code $code", 'access');
        } else {
            $this->setView('500');
            Debugger::log($exception, Debugger::ERROR);
        }
    }
}
