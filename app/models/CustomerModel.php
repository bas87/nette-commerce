<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CustomerModel extends BaseModel
{
    /**
     * @param int $id
     * @return \Nette\Database\Table\ActiveRow
     */
    public function getById($id)
    {
        return $this->database->table('customer')->where('id', $id)->fetch();
    }
}