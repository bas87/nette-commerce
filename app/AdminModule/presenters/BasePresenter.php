<?php

namespace AdminModule;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
abstract class BasePresenter extends \BasePresenter
{
    /**
     * @return mixed 
     */
    final public function getModel()
    {
        return $this->context->modelLoader;
    }

    public function startup()
    {
        parent::startup();

        // Authentikace
        if (!$this->user->isLoggedIn() && $this->name !== 'Admin:Sign') {
            if ($this->user->logoutReason === \Nette\Http\UserStorage::INACTIVITY) {
                $this->flashMessage('Přihlášení se nezdařilo');
            }
            $this->redirect('Sign:in');
        }
    }
}