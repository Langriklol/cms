<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
    public static function createRouter()
    {
        $router = new RouteList();
        $router[] = new Route('contact-us/', 'Core:Contact:default');
        $router[] = new Route('about/', 'Core:About:default');
        $router[] = new Route('<action>/', [
            'presenter' => 'Core:Administration',
            'action' => [
                Route::FILTER_TABLE => [
                    'administration' => 'default',
                    'login' => 'login',
                    'logout' => 'logout',
                    'register' => 'register'
                ],
                Route::FILTER_STRICT => true
            ]
        ]);
        $router[] = new Route('[<action>/][<url>]', array(
            'presenter' => 'Core:Article',
            'action' => array(
                Route::VALUE => 'default',
                Route::FILTER_TABLE => array(
                    'articles' => 'list',
                    'editor' => 'editor',
                    'remove' => 'remove'
                ),
                Route::FILTER_STRICT => true
            ),
            'url' => null,
        ));
        $router[] = new Route('[<url>]', 'Core:Article:default');
        return $router;
    }
}
