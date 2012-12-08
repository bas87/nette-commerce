<?php

namespace FrontModule;

use \Nette\Application\UI;
use \Nette\Application\UI\Form;
use \Nette\Mail\Message;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class OrderControl extends UI\Control
{
    /** @var mixed */
    public $model;

    /** @var \Nette\Mail\Message */
    public $mailer;

    /**
     * @param int $orderId 
     */
    private function sendMail($orderId)
    {
        $order = $this->model->order->getById($orderId);
        $customer = $this->model->customer->getById($order->customer_id);

        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $template = $this->template;
        $template->setFile(__DIR__ . '/email.latte');
        $template->order = $order;
        $template->customer = $customer;
        $template->shopName = $this->model->shop->name;
        $template->shopUrl = $this->model->shop->url;

        // Mail pro zákazníka
        $customerMail = clone $this->mailer;
        $customerMail->setFrom($this->model->shop->mail)
            ->addTo($customer->email)
            ->setSubject('Potvrzení objednávky č. ' . $order->number)
            ->setHtmlBody($template)->send();

        // Mail pro eshop
        $shopMail = clone $this->mailer;
        $shopMail->setFrom($customer->email)
            ->addTo($this->model->shop->mail)
            ->setSubject('Potvrzení objednávky č. ' . $order->number)
            ->setHtmlBody($template)->send();
    }

    /**
     * @param Form $form 
     */
    public function orderSubmitted(Form $form)
    {
        $data = $form->getValues();
        unset($data['confirm']);

        $orderId = $this->model->order->create($data);
        $this->sendMail($orderId);

        $this->presenter->redirect('Checkout:complete');
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/OrderControl.latte');
        $template->render();
    }

    /**
     * @return \Nette\Application\UI\Form 
     */
    protected function createComponentForm()
    {
        $form = new Form;
        $form->addText('first_name', 'Jméno:')->addRule(Form::FILLED, 'Zadejte prosím jméno.');
        $form->addText('last_name', 'Příjmení:')->addRule(Form::FILLED, 'Zadejte prosím příjmení.');
        $form->addText('company', 'Společnost:')->setOption('description', 'Nepovinný údaj');
        $form->addText('ic', 'IČ:')->setOption('description', 'Nepovinný údaj');
        $form->addText('dic', 'DIČ:')->setOption('description', 'Nepovinný údaj');
        $form->addText('email', 'E-mail:')->addRule(Form::FILLED, 'Zadejte prosím e-mail.')
            ->addRule(Form::EMAIL, 'E-mail není ve správném formátu.');
        $form->addText('phone', 'Telefon:')->addRule(Form::FILLED, 'Zadejte prosím telefon.');
        $form->addText('street', 'Ulice a č.p.:')->addRule(Form::FILLED, 'Zadejte prosím ulici.');
        $form->addText('city', 'Město:')->addRule(Form::FILLED, 'Zadejte prosím město.');
        $form->addText('zip', 'PSČ:')->addRule(Form::FILLED, 'Zadejte prosím PSČ.');
        $form->addSelect('state', 'Stát:', array('Česká republika'=>'Česká republika', 'Slovensko'=>'Slovensko'))
            ->addRule(Form::FILLED, 'Vyberte prosím stát.');
        $form->addSelect('delivery', 'Způsob doručení:', $this->model->delivery->getMethods()->fetchPairs('id', 'label'))
            ->setPrompt('Vyberte')
            ->addRule(Form::FILLED, 'Vyberte prosím způsob doručení.')->setOption('description', 'Přičítá se k ceně objednávky');
        $form->addSelect('payment', 'Způsob platby:', $this->model->payment->getMethods()->fetchPairs('id', 'label'))
            ->setPrompt('Vyberte')
            ->addRule(Form::FILLED, 'Vyberte prosím způsob platby.')->setOption('description', 'Přičítá se k ceně objednávky');
        $form->addTextArea('comment', 'Poznámka:');
        $form->addCheckbox('confirm', 'Souhlasím s podmínky')
                ->addRule(Form::EQUAL, 'Je potřeba souhlasit s obchodními podmínkami.', TRUE);
        $form->addSubmit('send', 'Objednat');

        $form->onSuccess[] = callback($this, 'orderSubmitted');
        return $form;     
    }
}