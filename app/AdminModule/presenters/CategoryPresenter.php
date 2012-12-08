<?php

namespace AdminModule;

use \Nette\Application\UI\Form;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CategoryPresenter extends BasePresenter
{
    /**
     * @param int $id 
     */
    public function renderEdit($id)
    {
        $form = $this->getComponent('category')->getComponent('form');
        $form->setValues($this->model->category->getById($id));
    }

    /**
     * @return CategoryControl 
     */
    public function createComponentCategory()
    {
        $product = new CategoryControl;
        $product->model = $this->model;
        return $product;
    }

    /**
     * @return CategoryListControl 
     */
    public function createComponentCategoryList()
    {
        $productList = new CategoryListControl;
        $productList->model = $this->model;
        return $productList;
    }
}