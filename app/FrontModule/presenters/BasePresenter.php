<?php

namespace FrontModule;

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

    /**
     * @return TopMenuControl 
     */
    protected function createComponentTopmenu()
    {
        return new TopMenuControl;
    }

    /**
     * @return TopSearchControl 
     */
    protected function createComponentTopsearch()
    {
        return new TopSearchControl;
    }

    /**
     * @return CatalogControl 
     */
    protected function createComponentCatalog()
    {
        $catalog = new CatalogControl;
        $catalog->model = $this->model;
        return $catalog;
    }

    /**
     * @return CartControl 
     */
    protected function createComponentCart()
    {
        $cart = new CartControl;
        $cart->model = $this->model;
        return $cart;
    }

    /**
     * @return CategoryControl 
     */
    protected function createComponentCategory()
    {
        $category = new CategoryControl;
        $category->model = $this->model;

        $_this = $this;
        $category->onClick[] = function($path) use ($_this) {
            $_this->context->session->getSection('category')->path = $path;
        };
        return $category;
    }
}
