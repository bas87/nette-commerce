<?php

namespace AdminModule;

use \Nette\Application\UI;
use \Nette\Application\UI\Form;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class ProductControl extends UI\Control
{
    /** @var mixed */
    public $model;

    /**
     * @param Form $form 
     */
    public function productSubmitted(Form $form)
    {
        if ($form->getValues()->id) {
            $this->model->catalog->editProduct($form->getValues());
        } else {
            $this->model->catalog->createProduct($form->getValues());
        }

        $this->presenter->flashMessage('Produkt byl uložen.');
        $this->presenter->redirect('Dashboard:default');
    }

    /**
     * @param \Nette\Forms\Controls\TextInput $item
     * @return boolean 
     */
    public function skuValidator(\Nette\Forms\Controls\TextInput $item)
    {
        $productData = $item->getParent()->getValues();
        $result = $this->model->catalog->getProductBySku($item->value);

        if (isset($result->id) && $productData['id'] == $result->id && (boolean) $result === TRUE) {
            return TRUE;
        } else {
            return !(boolean) $result;
        }
    }

    /**
     * @param \Nette\Forms\Controls\TextInput $item
     * @return boolean 
     */
    public function pathValidator(\Nette\Forms\Controls\TextInput $item)
    {
        $productData = $item->getParent()->getValues();
        $result = $this->model->catalog->getProductByPath($item->value);

        if (isset($result->id) && $productData['id'] == $result->id && (boolean) $result === TRUE) {
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

        $form->addGroup('Produkt');
        $form->addHidden('id');
        $form->addText('name', 'Jméno:')->addRule(Form::FILLED, 'Zadejte prosím jméno.');
        $form->addText('sku', 'SKU:')->addRule(Form::FILLED, 'Zadejte prosím SKU.')
                ->addRule(callback($this, 'skuValidator'), 'Hodnota SKU již existuje');
        $form->addTextArea('short_description', 'Krátký popis:')->addRule(Form::FILLED, 'Zadejte prosím krátký popis.');
        $form->addTextArea('description', 'Popis:')->addRule(Form::FILLED, 'Zadejte prosím popis.');
        $form->addText('path', 'Path:')->addRule(Form::FILLED, 'Zadejte prosím path.')
                ->addRule(callback($this, 'pathValidator'), 'Hodnota Path již existuje');
        $form->addCheckbox('homepage', 'Uvedení na homepage');
        $form->addText('price', 'Cena:')->addRule(Form::FILLED, 'Zadejte prosím cenu.');
        $form->addSelect('availability_id', 'Dostupnost:', $this->model->availability->getList()->fetchPairs('id', 'value'))
            ->addRule(Form::FILLED, 'Vyberte prosím dostupnost.');

        $form->addGroup('Kategorie');
        $categories = $form->addContainer('categories');
        $_this = $this;
        $categoriesList = function($categories, $parentId) use (&$categoriesList, $_this) {
            foreach ($_this->model->category->getChild($parentId) as $category) {
                $categories->addCheckbox($category->id, $category->name);
                $categoriesList($categories, $category->id);
            }
        };
        $categoriesList($categories, 1);

        $form->addGroup('Obrázky (při každém nahrání se předchozí verze nahradí)');
        $images = $form->addContainer('images');
        foreach (range(1, 10) as $id) {
            $images->addUpload($id, 'Obrázek ' . $id)->addCondition(Form::IMAGE)
                ->addRule(Form::IMAGE, 'Obrázek ' . $id . ' musí být JPEG, PNG nebo GIF.');
        }

        $form->addGroup('Manuály (při každém nahrání se předchozí verze nahradí)');
        $manuals = $form->addContainer('manuals');
        foreach (range(1, 1) as $id) {
            $manuals->addUpload($id, 'Manual ' . $id);
        }

        $form->addGroup('Zveřejnění');
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