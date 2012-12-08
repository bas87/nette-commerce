<?php

namespace FrontModule;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CatalogPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->getComponent('catalog')->setCategory($this->model->category->getByPath(NULL));
    }

    /**
     * @param string $query 
     */
    public function renderCategory($path)
    {
        $category = $this->model->category->getByPath($path);
        if($category === FALSE) {
            $this->flashMessage('Požadovanou kategorii se nepodařilo nalézt.');
            $this->forward('Catalog:default');
        }

        $this->getComponent('catalog')->setCategory($category);
        $this->getComponent('category')->setCurrent($category);
    }

    /**
     * @param string $query 
     */
    public function renderProduct($path)
    {
        if (isset($this->context->session->getSection('category')->path)) {
            $category = $this->model->category->getByPath(
                $this->context->session->getSection('category')->path);

            if (!$category) {
                $this->getComponent('category')->setCurrent($category);
            }
        }

        $product = $this->model->catalog->getProductByPath($path);
        if($product === FALSE) {
            $this->flashMessage('Požadovaný produkt se nepořilo nalézt.');
            $this->forward('Catalog:default');
        }

        $this->getComponent('product')->setProduct($product);
        $this->getComponent('comment')->setRelatedProduct($product);
        $this->template->product = $product;
    }

    /**
     * @param string $query 
     */
    public function renderSearch($query)
    {
        if(\Nette\Utils\Strings::length($query) == 0) {
            $this->flashMessage('Musíte zadat hledaný dotaz.');
            $this->redirect('Homepage:default');
        }
        $this->getComponent('catalog')->setSearchQuery($query);
    }

    /**
     * @return ProductControl 
     */
    protected function createComponentProduct()
    {
        $product = new ProductControl;
        $product->model = $this->model;
        return $product;
    }

    /**
     * @return CommentControl 
     */
    protected function createComponentComment()
    {
        $comment = new CommentControl;
        $comment->model = $this->model;
        $comment->mailer = $this->context->nette->createMail();
        return $comment;
    }
}