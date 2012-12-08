<?php

namespace FrontModule;

use Nette\Application\UI;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CategoryControl extends UI\Control
{
    /** @var mixed */
    public $model;

    /** @var \Nette\Database\Table\ActiveRow */
    private $current;

    /** @var array */
    public $onClick = array();

    /**
     * @param \Nette\Database\Table\ActiveRow $category 
     */
    public function setCurrent(\Nette\Database\Table\ActiveRow $category)
    {
        $this->current = $category;
    }

    /**
     * @param type string
     */
    public function handleSelectCategory($path)
    {
        $this->onClick($path);
        $this->presenter->redirect('Catalog:category', $path);
    }

    public function render()
    {
        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $template = $this->template;
        $template->setFile(__DIR__ . '/CategoryControl.latte');
        $template->category = $this->model->category;
        $template->current = $this->current;
        $template->render();
    }

}