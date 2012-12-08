<?php

namespace FrontModule;

use \Nette\Application\UI;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class TopMenuControl extends UI\Control
{
    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/TopMenuControl.latte');
        $template->render();
    }

}