<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 18.12.2018
 * Time: 13:29
 */

namespace OnlineImperium\Core;


use Nette\Application\Routers\RouteList;
use OnlineImperium\Globals;

/**
 * Class BaseRouterFactory
 * @package OnlineImperium\Core
 *
 * @link https://forum.nette.org/cs/26082-pridat-routu-v-extension-na-zaklade-modulu
 */
abstract class BaseRouterFactory
{
    /**
     * @var RouteList
     */
    protected $router;

    public function __construct() {
        $this->router = new RouteList();
    }

    public function append($route)
    {
        $this->router[] = $route;
    }

    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter()
    {
        $this->defineRouter($this->router);
        return $this->router;
    }

    protected abstract function defineRouter(RouteList $router);

    protected function t($message, $count = null, $parameters = [])
    {
        return Globals::t($message, $count, $parameters);
    }
}