<?php

namespace FrontModule;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class HomepagePresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->shopName = $this->model->shop->getName();
        $this->getComponent('catalog')->setHomepage();
    }
}