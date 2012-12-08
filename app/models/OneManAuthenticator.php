<?php

use \Nette\Object;
use \Nette\Security as NS;

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class OneManAuthenticator extends Object implements NS\IAuthenticator
{
    /** @var \Nette\ArrayHash */
    private $user;

    /**
     * @param string $id
     * @param string $password 
     */
    public function __construct($id, $password)
    {
        $this->user = \Nette\ArrayHash::from(array(
            'id' => $id,
            'password' => $password,
        ));
    }

    /**
     * @param array
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($id, $password) = $credentials;
        if (!$this->user) {
            throw new NS\AuthenticationException('User not found.', self::IDENTITY_NOT_FOUND);
        }

        if ($this->user->password !== $this->calculateHash($password)) {
            throw new NS\AuthenticationException('Invalid password.', self::INVALID_CREDENTIAL);
        }

        unset($this->user->password);
        return new NS\Identity($this->user->id, NULL, NULL);
    }

    /**
     * @param string $password
     * @return string 
     */
    public function calculateHash($password)
    {
        return md5($password . str_repeat('as4a2f45g3juon', 10));
    }
}