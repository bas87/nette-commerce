<?php

namespace FrontModule;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CartPresenter extends BasePresenter
{
    public function beforeRender()
    {
        parent::beforeRender();
        if ($this->model->cart->isEmpty()) {
            $this->flashMessage('Košík je prázdný.');
            $this->redirect('Homepage:default');
        }
    }
}