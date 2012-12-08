<?php

namespace AdminModule;

use \Nette\Application\UI\Form;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class SignPresenter extends BasePresenter
{
    public function actionOut()
    {
        $this->user->logout();
        $this->redirect('Sign:in');
    }

    /**
     * @return \Nette\Application\UI\Form 
     */
    public function createComponentForm()
    {
        $form = new Form;
        $form->addText('id', 'E-mail:')->addRule(Form::FILLED, 'Zadejte prosím e-mail.')
            ->addRule(Form::EMAIL, 'E-mail není ve správném formátu.');
        $form->addPassword('password', 'Heslo:')->addRule(Form::FILLED, 'Zadejte prosím heslo.');
        $form->addSubmit('send', 'Přihlásit');

        $_this = $this;
        $form->onSuccess[] = function ($form) use ($_this) {
            try {
                $values = $form->getValues();
                $_this->user->login($values->id, $values->password);
                $_this->presenter->redirect('Dashboard:default');

            } catch (\Nette\Security\AuthenticationException $e) {
                $_this->flashMessage('Přihlášení se nezdařilo');
            }
        };

        return $form;        
    }
}