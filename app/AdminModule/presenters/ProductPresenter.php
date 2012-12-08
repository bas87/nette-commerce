<?php

namespace AdminModule;

use \Nette\Application\UI\Form;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class ProductPresenter extends BasePresenter
{
    /**
     * @param int $id 
     */
    public function renderEdit($id)
    {
        $form = $this->getComponent('product')->getComponent('form');
        $form->setValues($this->model->catalog->getProductById($id));
        $form->getComponent('sku')->setAttribute('readonly');
        foreach ($this->model->category->getIdsByProductId($id) as $id) {
            $form->getComponent('categories')
                ->getComponent($id)->setValue(1);
        }
    }

    /**
     * @return ProductControl 
     */
    public function createComponentProduct()
    {
        $product = new ProductControl;
        $product->model = $this->model;
        return $product;
    }

    /**
     * @return ProductListControl 
     */
    public function createComponentProductList()
    {
        $productList = new ProductListControl;
        $productList->model = $this->model;
        return $productList;
    }
}