<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
abstract class BaseModel extends \Nette\Object
{
    /** @var \Nette\DI\Container */
    private $context;

    /** @var \Nette\Database\Connection */
    private $database;

    /* @var ModelLoader */
    protected $modelLoader;

    const VISIBLE = 1;
    const INVISIBLE = 0;

    /**
     * @param \Nette\DI\Container $container 
     */
    public function __construct(\Nette\DI\Container $container)
    {
        $this->context = $container;
    }

    /**
     * @return \Nette\DI\Container
     */
    final public function getContext()
    {
        return $this->context;
    }

    /**
     * @param \Nette\Database\Connection
     */
    final public function getDatabase()
    {
        return $this->context->database;
    }

    /**
     * @return mixed 
     */
    public function getModel()
    {
        return $this->modelLoader;
    }

    /**
     * @param ModelLoader $modelLoader
     */
    final public function addModelLoader(ModelLoader $modelLoader)
    {
        $this->modelLoader = $modelLoader;
    }
}