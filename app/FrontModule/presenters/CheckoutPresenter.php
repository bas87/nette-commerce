<?php

namespace FrontModule;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CheckoutPresenter extends BasePresenter
{
    public function renderDefault()
    {
        if ($this->model->cart->isEmpty()) {
            $this->flashMessage('Košík je prázdný.');
            $this->redirect('Homepage:default');
        }
    }

    /**
     * @return CheckoutControl 
     */
    protected function createComponentOrder()
    {
        $checkout = new OrderControl;
        $checkout->model = $this->model;
        $checkout->mailer = $this->context->nette->createMail();
        return $checkout;
    }
}