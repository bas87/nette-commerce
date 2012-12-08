<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
final class ModelLoader extends Nette\Object
{
    /** @var \Nette\DI\Container */
    private $modelContainer;

    /** @var array */
    private $models = array();

    /**
     * @param \Nette\DI\Container $container 
     */
    public function __construct(\Nette\DI\Container $container)
    {
        $modelContainer = new Nette\DI\Container;
        $modelContainer->addService('database', $container->database);
        $modelContainer->addService('cacheStorage', $container->cacheStorage);
        $modelContainer->addService('session', $container->session);
        $modelContainer->addService('httpRequest', $container->httpRequest);
        $modelContainer->freeze();
        $this->modelContainer = $modelContainer;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getModel($name)
    {
        if (!isset($this->models[$name])) {
            $class = \ucfirst($name) . 'Model';

            if (!\class_exists($class)) {
                throw new \Nette\InvalidArgumentException("Model '$class' not found");
            }

            $this->models[$name] = new $class($this->modelContainer);
	    $this->models[$name]->addModelLoader($this);
        }

        return $this->models[$name];
    }

    /**
     * @param string $name
     * @return mixed 
     */
    public function &__get($name)
    {
        try {
            $result = $this->getModel($name);
        } catch (\Nette\InvalidArgumentException $e) {
             parent::__get($name);       
        }

        return $result;
    }
}