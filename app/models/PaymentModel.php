<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class PaymentModel extends BaseModel
{
    /**
     * @return \Nette\Database\Table\Selection
     */
    public function getMethods()
    {
        return $this->database->table('payment')->select('id')
            ->select('CONCAT(method, " (+" , price, " Kč)") AS label');
    }
}