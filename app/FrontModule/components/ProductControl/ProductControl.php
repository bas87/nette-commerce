<?php

namespace FrontModule;

use \Nette\Application\UI;
use \Nette\Application\UI\Form;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class ProductControl extends UI\Control
{
    /** @var mixed */
    public $model;

    /** @var \Nette\Database\Table\ActiveRow */
    private $product;

    /**
     * @param \Nette\Database\Table\ActiveRow $product 
     */
    public function setProduct(\Nette\Database\Table\ActiveRow $product)
    {
        $this->product = $product;
    }

    public function render()
    {
        $this->getComponent('form')
            ->getComponent('product_id')->setValue($this->product->id);

        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $template = $this->template;
        $template->setFile(__DIR__ . '/ProductControl.latte');
        $template->product = $this->product;
        $template->render();
    }

    /**
     * @return \Nette\Application\UI\Form 
     */
    public function createComponentForm()
    {
        $model = $this->model;
        $_this = $this;

        $form = new Form;
        $form->addHidden('product_id');
        $form->addText('amount', 'Množství:')->setValue(1)
            ->addRule(Form::FILLED, 'Zadejte prosím množství.')
            ->addRule(Form::INTEGER, 'Množství musí být číslo.')
            ->addRule(function ($item) { return $item->value <= 0 ? FALSE : TRUE; },
                    'Množství musí být větší jak nula.');
        $form->addSubmit('send', 'Do košíku');

        $form->onSuccess[] = function ($form) use ($model, $_this) {
            $values = $form->getValues();
            $values['identity'] = $model->user->getHostId();
            $model->cart->insertProduct($values);

            $_this->presenter->flashMessage('Zboží bylo přidáno do košíku.');
            $_this->presenter->redirect('Cart:default');
        };

        return $form;        
    }
}