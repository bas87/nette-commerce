<?php

namespace FrontModule;

use \Nette\Application\UI;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CatalogControl extends UI\Control
{
    /** @persistent */
    public $page = 1;

    /** @var mixed */
    public $model;

    /** @var \Nette\Database\Table\ActiveRow */
    private $category;

    /** @var boolean */
    private $homepage;

    /** @var string */
    private $searchQuery;

    /**
     * @param \Nette\Database\Table\ActiveRow $category 
     */
    public function setCategory(\Nette\Database\Table\ActiveRow $category)
    {
        $this->category = $category;
    }

    /**
     * @param boolean $is 
     */
    public function setHomepage($is = TRUE)
    {
        $this->homepage = $is;
    }

    /**
     * @param string $searchQuery 
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;
    }

    /**
     * @param int $id 
     */
    public function handleAddToCart($id)
    {
        $this->model->cart->insertProduct(\Nette\ArrayHash::from(array(
            'identity' => $this->model->user->getHostId(),
            'product_id' => $id,
            'amount' => 1,
        )));

        $this->presenter->flashMessage('Zboží bylo přidáno do košíku.');
        $this->presenter->redirect('this');
    }

    public function render()
    {
        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        if (\Nette\Utils\Strings::length($this->searchQuery) > 0) {
            $products = $this->model->catalog->getProductsBySearchQuery($this->searchQuery);
        } else if ($this->homepage) {
            $products = $this->model->catalog->getProductsOnHomepage();
        } else {
            $products = $this->model->catalog->getProductsByCategory($this->category);
        }

        $paginator = new \Nette\Utils\Paginator;
        $paginator->setItemCount($products->count());
        $paginator->setItemsPerPage(6);
        $paginator->setPage($this->page);

        $template = $this->template;
        $template->setFile(__DIR__ . '/CatalogControl.latte');
        $template->paginator = $paginator;
        $template->model = $this->model;
        $template->products = $products->order('id DESC')
            ->limit($paginator->getLength(), $paginator->getOffset());
        $template->render();
    }
}