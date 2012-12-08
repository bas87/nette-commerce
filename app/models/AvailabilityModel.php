<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class AvailabilityModel extends BaseModel
{
    /**
     * @return \Nette\Database\Table\Selection
     */
    public function getList()
    {
        return $this->database->table('availability')
            ->order('id ASC');
    }
}