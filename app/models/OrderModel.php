<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class OrderModel extends BaseModel
{
    /**
     * @param int $id
     * @return \Nette\Database\Table\ActiveRow
     */
    public function getById($id)
    {
        return $this->database->table('order')->where('id', $id)->fetch();
    }

    /**
     * @param \Nette\ArrayHash $oderData
     * @return int
     */
    public function create(\Nette\ArrayHash $oderData)
    {
        $customerData = clone $oderData;
        unset($customerData['delivery'], $customerData['payment'], $customerData['comment']);

        $customerId = $customerData['id'] = $this->database->table('customer')->max('id') + 1;
        $this->database->exec('INSERT INTO `customer`', $customerData);

        $orderId = $this->database->table('order')->max('id') + 1;
        $orderNumber = $this->database->table('order')->max('number') + 1;
        $this->database->exec('INSERT INTO `order`', array(
            'id' => $orderId,
            'number' => $orderNumber,
            'customer_id' => $customerId,
            'creation' => new \Nette\DateTime,
            'total' => $this->model->cart->getSumary()->total,
            'delivery_id' => $oderData['delivery'],
            'payment_id' => $oderData['payment'],
            'comment' => $oderData['comment'],
        ));

        foreach($this->model->cart->getList() as $product) {
            $this->database->exec('INSERT INTO `order_product`', array(
                'order_id' => $orderId,
                'product_id' => $product->product_id,
                'amount' => $product->amount,
            ));
        }
        $this->model->cart->removeAllProducts();

        return $orderId;
    }
}