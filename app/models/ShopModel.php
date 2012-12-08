<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class ShopModel extends BaseModel
{
    /** @var string */
    private $name = 'quadrocopter.cz';

    /** @var string */
    private $url = 'http://www.quadrocopter.cz';

    /** @var string */
    private $mail = 'Obchod <info@quadrocopter.cz>';

    /**
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }
}