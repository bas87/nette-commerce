<?php

namespace FrontModule;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class XmlFeedPresenter extends BasePresenter
{
    /**
     * XML feed pro zbozi.cz
     */
    public function renderSeznam()
    {
        $this->context->httpResponse->setContentType('text/xml', 'utf-8');

        // Root
        $eDom = new \DOMDocument('1.0', 'utf-8');
        $eShop = $eDom->createElement('SHOP');

        foreach ($this->model->catalog->getProducts() as $product) {

            // Produkt
            $eShopitem = $eDom->createElement('SHOPITEM');
            $eShop->appendChild($eShopitem);

            // Jméno produktu
            $eProduct = $eDom->createElement('PRODUCT', $product->name);
            $eShopitem->appendChild($eProduct);

            // Popis
            $eDescription = $eDom->createElement('DESCRIPTION', $product->short_description);
            $eShopitem->appendChild($eDescription);

            // Url na produkt
            $eUrl = $eDom->createElement('URL', $this->model->shop->url . $this->link('Catalog:product', $product->path));
            $eShopitem->appendChild($eUrl);

            // Url na obrázek (nepovinné)
            if($pi = $this->model->catalog->getProductPromoImage($product)) {
                $eImgUrl = $eDom->createElement('IMGURL', $this->model->shop->url . $pi->path);
                $eShopitem->appendChild($eImgUrl);
            }

            // Cena s DPH
            $ePriceVat = $eDom->createElement('PRICE_VAT',  $product->price);
            $eShopitem->appendChild($ePriceVat);

            $eDom->appendChild($eShop);
        }

        $this->sendResponse(new \Nette\Application\Responses\TextResponse($eDom->saveXML()));
    }

    /**
     * XML feed pro heureka.cz
     */
    public function renderGoogle()
    {
        // TODO: dořešit
    }
}