<?php

namespace AdminModule;

use \Nette\Application\UI;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CategoryListControl extends UI\Control
{
    /** @var mixed */
    public $model;

    /** @var array */
    public $categories = array();

    /**
     * @return \Nette\ArrayHash 
     */
    public function getCategories()
    {
        $_this = $this;
        $categoriesList = function($parentId) use (&$categoriesList, $_this) {
            foreach ($_this->model->category->getChild($parentId) as $category) {
                $_this->categories[] = $category;
                $categoriesList($category->id);
            }
        };

        $categoriesList(1);
        return \Nette\ArrayHash::from($this->categories);
    }

    public function render()
    {
        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $template = $this->template;
        $template->setFile(__DIR__ . '/CategoryListControl.latte');
        $template->categories = $this->getCategories();
        $template->render();
    }
}