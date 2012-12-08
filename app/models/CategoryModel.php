<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CategoryModel extends BaseModel
{
    /** @var array */
    private $childsIds = array();

    /** @var array */
    private $invisibleIds = array();

    /**
     * @param int $parentId
     * @return \Nette\Database\Table\Selection
     */
    public function getChild($paretId)
    {
        return $this->database->table('category')
            ->where('parent_id', $paretId)->where('visibility', self::VISIBLE)
                ->order('position');
    }

    /**
     * @param int $id
     * @return \Nette\Database\Table\ActiveRow
     */
    public function getById($id)
    {
        return $this->database->table('category')->where('id', $id)->fetch();
    }

    /**
     * @param string $path
     * @return \Nette\Database\Table\ActiveRow
     */
    public function getByPath($path)
    {
        return $this->database->table('category')->where('path', $path)->fetch();
    }

    /**
     * @param int $parentId
     * @return array
     */
    public function getFamilyIds($paretId, $isInterated = FALSE)
    {
        if (!$isInterated) {
            $this->childsIds = array();
            $this->invisibleIds = array();
        }

        $this->childsIds[] = $paretId;
        foreach ($this->getChild($paretId) as $category) {
            if ($category->visibility == self::INVISIBLE) {
                $this->invisibleIds = $category->id;
            }

            $this->childsIds[] = $category->id;
            $this->getFamilyIds($category->id, TRUE);
        }
        return \array_diff(\array_unique($this->childsIds), $this->invisibleIds);
    }

    /**
     * @param int $productId
     * @return \Nette\ArrayHash
     */
    public function getIdsByProductId($productId)
    {
        return $this->database->table('category_product')
            ->where('product_id', $productId)
                ->fetchPairs('category_id', 'category_id');
    }

    /**
     * @param Nette\ArrayHash $categoryData
     * @return int
     */
    public function create(\Nette\ArrayHash $categoryData)
    {
        $categoryId = $categoryData['id'] = $this->database->table('category')->max('id') + 1;
        $this->database->exec('INSERT INTO `category`', $categoryData);
        return $categoryId;
    }

    /**
     * @param Nette\ArrayHash $categoryData
     * @return int
     */
    public function edit(\Nette\ArrayHash $categoryData)
    {
        $categoryId = $categoryData['id'];
        unset($categoryData['id']);
        $this->database->exec('UPDATE `category` SET ? WHERE `id` = ?', $categoryData, $categoryId);
        return $categoryId;
    }
}