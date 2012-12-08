<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class UserModel extends BaseModel
{
    /**
     * @return string
     */
    public function getHostId()
    {
        $host = $this->context->session->getSection('host');
        if (!isset($host->id)) {
            $host->id = sha1(uniqid(time()));
        }
        
        return $host->id;
    }
}