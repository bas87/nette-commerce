<?php

namespace AdminModule;

use \Nette\Application\UI;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class ProductListControl extends UI\Control
{
    /** @persistent */
    public $page = 1;

    /** @var mixed */
    public $model;

    public function render()
    {
        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $products = $this->model->catalog->getProducts();

        $paginator = new \Nette\Utils\Paginator;
        $paginator->setItemCount($products->count());
        $paginator->setItemsPerPage(6);
        $paginator->setPage($this->page);

        $template = $this->template;
        $template->setFile(__DIR__ . '/ProductListControl.latte');
        $template->paginator = $paginator;
        $template->products = $products->order('id DESC')
            ->limit($paginator->getLength(), $paginator->getOffset());
        $template->render();
    }
}