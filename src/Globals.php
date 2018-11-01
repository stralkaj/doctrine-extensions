<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 07.02.2018
 * Time: 9:52
 */

namespace OnlineImperium;
use Nette\Application\Application;
use Nette\SmartObject;

/**
 * Class Globals
 */
class Globals
{
    use SmartObject;

    /**
     * @var Application
     */
    protected $application;


    protected $parameters;

    public function __construct(Application $application)
    {
        global $container;

        assert($application != null);
        $this->application = $application;//$container->getService('application');
        $this->parameters = $container->getParameters();
    }

    /**
     * @return Globals
     */
    public static function instance()
    {
        global $container;
        return $container->getByType(self::class);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public static function getParameter($path = null)
    {
        if ($path) {
            $parts = explode('/', $path);
            $param = self::instance()->parameters;
            foreach ($parts as $part) {
                if (!isset($param[$part])) {
                    return null;
                }
                $param = $param[$part];
            }
            return $param;
        } else {
            return self::instance()->parameters;
        }
    }

    /**
     * @return \App\Model\DaoManager
     */
    public static function dao()
    {
        global $container;
        return $container->getService("dao");
    }

    public static function link($destination, $args = [])
    {
        $presenter = self::instance()->application->getPresenter();
        if ($presenter) {
            return $presenter->link($destination, $args);
        }

        $linkGenerator = self::getService('application.linkGenerator');
        if (strpos($destination, '//') === 0) {
            // LinkGenerator nepracuje s // a vse vraci automaticky absolutne
            $destination = substr($destination, 2);
        }
        return $linkGenerator->link($destination, $args);
    }

    public static function t($message, $count = null, $parameters = [])
    {
        return self::getService(\Kdyby\Translation\Translator::class);
        //TODO toto asi nebude fungovat z console!!!
        //$translator = self::instance()->application->getPresenter()->translator;
        $translator = self::instance()->translator;
        return $translator->translate($message, $count, $parameters);
    }

    public static function getService($name)
    {
        global $container;
        $service = $container->getByType($name, false);
        if (!$service) {
            $service = $container->getService($name);
        }
        return $service;
    }

    public static function getSession()
    {
        return self::getService('session');
    }
}