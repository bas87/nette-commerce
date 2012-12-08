<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CartModel extends BaseModel
{
    /**
     * @return \Nette\ArrayHash
     */
    public function getSumary()
    {
        $result = $this->database->query('
            SELECT `cart`.`amount` AS `amount`, (`product`.`price` * `cart`.`amount`) AS `total`
            FROM `cart`
            JOIN `product` ON `product`.`id` = `cart`.`product_id`
            WHERE `cart`.`identity` = ?', $this->model->user->getHostId());

        $sumary = array();
        $sumary['amount'] = 0;
        $sumary['total'] = 0;

        foreach ($result as $row) {
            $sumary['total'] = $sumary['total'] + $row->total;
            $sumary['amount'] = $sumary['amount'] + $row->amount;
        }

        return \Nette\ArrayHash::from($sumary);
    }

    /**
     * @return \Nette\Database\Table\Selection
     */
    public function getList()
    {
        return $this->database->table('cart')
                ->where('identity', $this->model->user->getHostId());
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->getList()->count() == 0 ? TRUE : FALSE;
    }

    /**
     * @param int $id
     * @return int
     */
    public function removeProduct($id)
    {
        return $this->database->exec('DELETE FROM `cart` WHERE `id` = ?', $id);
    }

    /**
     * @return int
     */
    public function removeAllProducts()
    {
        $affectedRows = 0;
        foreach ($this->getList() as $cart) {
            $affectedRows = $this->removeProduct($cart->id) + $affectedRows;
        }
        return $affectedRows;
    }

    /**
     * @param \Nette\ArrayHash $data
     * @return int
     */
    public function insertProduct(\Nette\ArrayHash $data)
    {
        $cartItem = $this->database->table('cart')->where(array(
            'identity' => $data['identity'],
            'product_id'=> $data['product_id'],
        ))->fetch();

        $affectedRows = 0;
        if($cartItem) {
           $affectedRows = $this->database->exec('UPDATE `cart` SET `amount` = ? WHERE `id` = ?', \abs($cartItem->amount + $data['amount']), $cartItem->id);
        }  else {
           $affectedRows = $this->database->exec('INSERT INTO `cart`', $data);
        }
        return $affectedRows; 
    }
}