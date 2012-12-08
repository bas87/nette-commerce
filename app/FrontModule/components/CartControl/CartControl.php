<?php

namespace FrontModule;

use \Nette\Application\UI;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CartControl extends UI\Control
{
    /** @var mixed */
    public $model;

    /**
     * @param int $id 
     */
    public function handleRemove($id)
    {
        $this->model->cart->removeProduct($id);

        $this->presenter->flashMessage('Zboží bylo odstraněno z košíku.');
        $this->presenter->redirect('this');
    }

    public function renderPanel()
    {
        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $template = $this->template;
        $template->setFile(__DIR__ . '/CartControlPanel.latte');
        $template->sumary = $this->model->cart->getSumary();
        $template->render();
    }

    public function renderList()
    {
        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $template = $this->template;
        $template->setFile(__DIR__ . '/CartControlList.latte');
        $template->cart = $this->model->cart->getList();
        $template->sumary = $this->model->cart->getSumary();
        $template->render();
    }

    /**
     * PATCH for Invalidation components whit many templates
     */
    public function render()
    {
        $this->renderPanel();
        //$this->renderList();
    }
}