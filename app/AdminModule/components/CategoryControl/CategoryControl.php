<?php

namespace AdminModule;

use \Nette\Application\UI;
use \Nette\Application\UI\Form;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CategoryControl extends UI\Control
{
    /** @var mixed */
    public $model;

    /** @var array */
    public $categories = array();

    /**
     * @param int $selfId
     * @return \Nette\ArrayHash 
     */
    public function getCategories($selfId = NULL)
    {
        $_this = $this;
        $this->categories[1] = 'Root';

        $categoriesList = function($parentId) use (&$categoriesList, $_this) {
            foreach ($_this->model->category->getChild($parentId) as $category) {
                $_this->categories[$category->id] = $category->name;
                $categoriesList($category->id);
            }
        };

        $categoriesList(1);
        return \Nette\ArrayHash::from(
            array_diff_key($this->categories, array($selfId => $selfId)));
    }

    /**
     * @param Form $form 
     */
    public function productSubmitted(Form $form)
    {
        if ($form->getValues()->id) {
            $this->model->category->edit($form->getValues());
        } else {
            $this->model->category->create($form->getValues());
        }

        $this->presenter->flashMessage('Kategorie byla uložena.');
        $this->presenter->redirect('Dashboard:default');
    }

    /**
     * @param \Nette\Forms\Controls\TextInput $item
     * @return boolean 
     */
    public function pathValidator(\Nette\Forms\Controls\TextInput $item)
    {
        $categoryData = $item->getParent()->getValues();
        $result = $this->model->category->getByPath($item->value);

        if (isset($result->id) && $categoryData['id'] == $result->id && (boolean) $result === TRUE) {
            return TRUE;
        } else {
            return !(boolean) $result;
        }
    }

    /**
     * @return \Nette\Application\UI\Form 
     */
    protected function createComponentForm()
    {
        $form = new Form;

        $form->addGroup('Kategorie');
        $form->addHidden('id');
        $form->addText('name', 'Jméno:')->addRule(Form::FILLED, 'Zadejte prosím jméno.');
        $form->addSelect('parent_id', 'Nadřazená kategorie', (array) $this->getCategories());
        $form->addText('position', 'Pozice:')->addRule(Form::FILLED, 'Zadejte prosím pozici.')
                ->addRule(Form::INTEGER, 'Pozice musí být číslo.');
        $form->addText('path', 'Path:')->addRule(Form::FILLED, 'Zadejte prosím path.')
                ->addRule(callback($this, 'pathValidator'), 'Hodnota Path již existuje');
        $form->addCheckbox('visibility', 'Zobrazit po uložení')->setValue(TRUE);
        $form->addSubmit('send', 'Publikovat');

        $form->onSuccess[] = callback($this, 'productSubmitted');
        return $form;  
    }

    public function render()
    {
        $this->getComponent('form')->render();
    }
}