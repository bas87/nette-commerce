<?php

namespace FrontModule;

use \Nette\Application\UI;
use \Nette\Application\UI\Form;
use \Nette\Mail\Message;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CommentControl extends UI\Control
{
    /** @var mixed */
    public $model;

    /** @var \Nette\Mail\Message */
    public $mailer;

    /** @var \Nette\Database\Table\ActiveRow */
    private $relatedProduct;

    /**
     * @param \Nette\Database\Table\ActiveRow $product 
     */
    public function setRelatedProduct(\Nette\Database\Table\ActiveRow $product)
    {
        $this->relatedProduct = $product;
    }

    /**
     * @param int $productId
     * @param \Nette\Database\Table\ActiveRow $parentComment 
     */
    private function sendBuzzMail($productId)
    {
        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $template = $this->template;
        $template->setFile(__DIR__ . '/email/buzz.latte');
        $template->productUrl = $this->model->shop->url . $this->presenter->link('Catalog:product',
                $this->model->catalog->getProductById($productId)->path);
        $template->shopName = $this->model->shop->name;
        $template->shopUrl = $this->model->shop->url;

        // Mail pro eshop
        $shopMail = clone $this->mailer;
        $shopMail->setFrom($this->model->shop->mail)
            ->addTo($this->model->shop->mail)
            ->setSubject('Reakce na produkt')
            ->setHtmlBody($template)->send();
    }

    /**
     * @param \Nette\Database\Table\ActiveRow $parentComment 
     */
    private function sendCommentReplyMail(\Nette\Database\Table\ActiveRow $parentComment)
    {
        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $template = $this->template;
        $template->setFile(__DIR__ . '/email/reply.latte');
        $template->productUrl = $this->model->shop->url . $this->presenter->link('Catalog:product',
                $this->model->catalog->getProductById($parentComment->product_id)->path);
        $template->shopName = $this->model->shop->name;
        $template->shopUrl = $this->model->shop->url;

        // Mail pro diskutára
        $customerMail = clone $this->mailer;
        $customerMail->setFrom($this->model->shop->mail)
            ->addTo($parentComment->email)
            ->setSubject('Reakce na Váš komentář')
            ->setHtmlBody($template)->send();

        // Mail pro eshop
        $shopMail = clone $this->mailer;
        $shopMail->setFrom($parentComment->email)
            ->addTo($this->model->shop->mail)
            ->setSubject('Reakce na komentář')
            ->setHtmlBody($template)->send();
    }

    /**
     * @param Form $form 
     */
    public function commentSubmitted(Form $form)
    {
        $data = $form->getValues();
        $this->model->comment->create($data);

        if (!empty($data['parent_id'])) {
            $parentComment = $this->model->comment->getById($data['parent_id']);
            if(\Nette\Utils\Strings::length($parentComment->email) > 0) {
                $this->sendCommentReplyMail($parentComment);
            }
        } else {
            $this->sendBuzzMail($data['product_id']);
        }

        $this->presenter->flashMessage('Komentář byl přidán do diskuse.');
        $this->presenter->redirect('this');
    }

    /**
     * @param int $commentId 
     */
    public function handleReply($commentId)
    {
        $this->getComponent('form')
            ->getComponent('parent_id')->setValue($commentId);
    }

    public function render()
    {
        $this->getComponent('form')
            ->getComponent('product_id')->setValue($this->relatedProduct->id);

        $helpers = new \MagicHelpers($this->getPresenter()->getContext());
        $this->template->registerHelperLoader(\callback($helpers, 'loader'));

        $template = $this->template;
        $template->setFile(__DIR__ . '/CommentControl.latte');
        $template->model = $this->model;
        $template->comments = $this->model->comment->getByProductId($this->relatedProduct->id);
        $template->render();
    }

    /**
     * @return \Nette\Application\UI\Form 
     */
    protected function createComponentForm()
    {
        $form = new Form;
        $form->addHidden('product_id');
        $form->addHidden('parent_id')->setValue(0);
        $form->addText('name', 'Jméno:')->addRule(Form::FILLED, 'Zadejte prosím jméno.');
        $form->addText('email', 'E-mail:')->addCondition(Form::FILLED)
                ->addRule(Form::EMAIL, 'E-mail není ve správném formátu.');
        $form->addTextArea('message', 'Text:')->addRule(Form::FILLED, 'Zadejte prosím text.');
        $form->addSubmit('send', 'Odeslat');

        $form['email']->setOption('description', 'slouží pro upozornění na odpověď, nebude se zobrazovat');
        $form->onSuccess[] = callback($this, 'commentSubmitted');
        return $form;     
    }
}