<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CatalogModel extends BaseModel
{
    /**
     * @return \Nette\Database\Table\Selection
     */
    public function getProducts()
    {
        return $this->database->table('product')
            ->where('visibility', self::VISIBLE);
    }

    /**
     * @return \Nette\Database\Table\Selection
     */
    public function getProductsOnHomepage()
    {
        return $this->getProducts()->where('homepage = ?', TRUE);
    }

    /**
     * @param \Nette\Database\Table\ActiveRow $category
     * @return \Nette\Database\Table\Selection
     */
    public function getProductsByCategory(\Nette\Database\Table\ActiveRow $category)
    {
        return $this->getProducts()->where('id',
            $this->database->table('category_product')->select('product_id')
                ->where('category_id', $this->model->category->getFamilyIds($category->id)));
    }

    /**
     * @param string
     * @return \Nette\Database\Table\Selection
     */
    public function getProductsBySearchQuery($searchQuery)
    {
        return $this->getProducts()
            ->where('name LIKE ? OR short_description LIKE ? OR description LIKE ?', "%$searchQuery%", "%$searchQuery%", "%$searchQuery%");
    }

    /**
     * @param \Nette\Database\Table\ActiveRow $product
     * @return \Nette\Database\Table\ActiveRow 
     */
    public function getProductPromoImage(\Nette\Database\Table\ActiveRow $product)
    {
        $result = $product->related('product_image')->limit(1)->fetch();
        return $result !== FALSE ?  $result->image : FALSE;
    }

    /**
     * @param string $path
     * @return \Nette\Database\Table\ActiveRow
     */
    public function getProductByPath($path)
    {
        return $this->database->table('product')->where('path', $path)->fetch();
    }

    /**
     * @param int $id
     * @return \Nette\Database\Table\ActiveRow
     */
    public function getProductById($id)
    {
        return $this->database->table('product')->where('id', $id)->fetch();
    }

    /**
     * @param int $sku
     * @return \Nette\Database\Table\ActiveRow
     */
    public function getProductBySku($sku)
    {
        return $this->database->table('product')->where('sku', $sku)->fetch();
    }

    /**
     * @param type $productId
     * @param \Nette\ArrayHash $productData 
     */
    private function uploadProductMedia($productId, \Nette\ArrayHash $productData)
    {

        $images = array();
        $imagesCounter = 1;
        foreach($productData['images'] as $image) {
            if ($image->isOk()) {
                $imagePath = "/media/products/images/{$productData['sku']}/{$imagesCounter}." . 
                        \pathinfo($image->getName(), \PATHINFO_EXTENSION);
                
                if(!\file_exists(\dirname(\WWW_DIR . $imagePath))) {
                    \mkdir(\dirname(\WWW_DIR . $imagePath), 0775, TRUE);
                }
                $image->move(\WWW_DIR . $imagePath);

                $imageId = $images[] = $this->database->table('image')->max('id') + 1;
                $this->database->exec('INSERT INTO `image`', array(
                    'id' => $imageId,
                    'name' => 'Náhled',
                    'path' => $imagePath,
                ));
                $imagesCounter++;
            }
        }

        foreach($images as $imageId) {
            $this->database->exec('INSERT INTO `product_image`', array(
                'product_id' => $productId,
                'image_id' => $imageId
            ));
        }

        $manualsCounter = 1;
        foreach($productData['manuals'] as $manual) {
            if ($manual->isOk()) {
                $manualPath = "/media/products/manuals/{$productData['sku']}/{$manualsCounter}." . 
                        \pathinfo($manual->getName(), \PATHINFO_EXTENSION);

                if(!\file_exists(\dirname(\WWW_DIR . $manualPath))) {
                    \mkdir(\dirname(\WWW_DIR . $manualPath), 0775, TRUE);
                }
                $manual->move(WWW_DIR . $manualPath);
            }
            $manualsCounter++;
        }
    }

    /**
     * @param Nette\ArrayHash $productData
     * @return int
     */
    public function createProduct(\Nette\ArrayHash $productData)
    {
        $product = clone $productData;
        unset($product['categories'], $product['images'], $product['manuals']);

        $productId = $product['id'] = $this->database->table('product')->max('id') + 1;
        $this->database->exec('INSERT INTO `product`', $product);

        foreach($productData['categories'] as $categoryId => $pair) {
            if ($pair) {
                $this->database->exec('INSERT INTO `category_product`', array(
                    'category_id' => $categoryId,
                    'product_id' => $productId
                ));
            }
        }

        $this->uploadProductMedia($productId, $productData);
        return $productId;
    }

    /**
     * @param Nette\ArrayHash $productData
     * @return int
     */
    public function editProduct(\Nette\ArrayHash $productData)
    {
        $product = clone $productData;
        unset($product['sku'], $product['categories'], $product['images'], $product['manuals']);

        $this->database->exec('UPDATE `product` SET ? WHERE `id` = ?', $product, $product->id);
        $this->database->exec('DELETE FROM `category_product` WHERE `product_id` = ?', $product->id);

        foreach($productData['categories'] as $categoryId => $pair) {
            if ($pair) {
                $this->database->exec('INSERT INTO `category_product`', array(
                    'category_id' => $categoryId,
                    'product_id' => $product->id
                ));
            }
        }

        $countUploadedImages = 0;
        foreach ($productData['images'] as $image) {
            if($image->isOk()) {
                $countUploadedImages++;
            }   
        }

        if ($countUploadedImages > 0) {
            $images = $this->database->table('image')->where('id', $this->database->table('product_image')
                ->select('image_id')->where('product_id = ?', $product->id));

            $this->database->exec('DELETE FROM `product_image` WHERE `product_id` = ?', $product->id);
            foreach($images as $image) {
                $this->database->exec('DELETE FROM `image` WHERE `id` = ?', $image->id);
                if (\file_exists(\WWW_DIR . $image->path)) {
                    unlink(\WWW_DIR . $image->path);
                }
            }
        }

        $countUploadedManuals = 0;
        foreach ($productData['manuals'] as $manual) {
            if($manual->isOk()) {
                $countUploadedManuals++;
            }   
        }

        if ($countUploadedManuals > 0) {
            foreach(\Nette\Utils\Finder::findFiles('*')->in(\WWW_DIR . "/media/products/manuals/{$productData['sku']}") as $file) {
                unlink($file->getPathname());
            }
        }

        $this->uploadProductMedia($product->id, $productData);
        return $product->id;
    }
}