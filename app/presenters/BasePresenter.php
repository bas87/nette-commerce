<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    protected function beforeRender()
    {
        parent::beforeRender();
        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));
    }
}
