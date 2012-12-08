<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
use Nette\Diagnostics\Debugger,
    Nette\Application\Routers\Route,
    Nette\Application\Routers\RouteList,
    Nette\Application\Routers\SimpleRouter;


// Načte Nette Framework
require LIBS_DIR . '/Nette/loader.php';

// Konfigurace apliakce
$configurator = new Nette\Config\Configurator;
$configurator->setProductionMode(FALSE);
$configurator->setTempDirectory(__DIR__ . '/../temp');


// Zapne Nette Debugger
$configurator->enableDebugger(__DIR__ . '/../log', 'toman@devzone.cz');


// Zapnene robotloader
$configurator->createRobotLoader()
        ->addDirectory(APP_DIR)
        ->addDirectory(LIBS_DIR)
        ->register();


// Vytvoří DI pro config.neon
$configurator->addConfig(__DIR__ . '/config/config.neon', $configurator::DEVELOPMENT);
$container = $configurator->createContainer();


// Nastavení routeru
if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {
    $container->router[] = new Route('index.php', 'Front:Homepage:default', Route::ONE_WAY);

    $container->router[] = $adminRouter = new RouteList('Admin');
    $adminRouter[] = new Route('admin/<presenter>/<action>', 'Dashboard:default');

    $container->router[] = $frontRouter = new RouteList('Front');
    $frontRouter[] = new Route('catalog/category/[<path .+>]', array(
                'presenter' => 'Catalog',
                'action' => 'category',
                'path' => array(
                    Route::VALUE => NULL,
                    Route::FILTER_IN => NULL,
                    Route::FILTER_OUT => NULL,
                ),
            ));
    $frontRouter[] = new Route('catalog/product/[<path .+>]', array(
                'presenter' => 'Catalog',
                'action' => 'product',
                'path' => array(
                    Route::VALUE => NULL,
                    Route::FILTER_IN => NULL,
                    Route::FILTER_OUT => NULL,
                ),
            ));
    $frontRouter[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
} else {
    $container->router = new SimpleRouter('Front:Homepage:default');
}


// Start aplikace 
$container->application->run();
