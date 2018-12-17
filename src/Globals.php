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
     * DI instance of this class
     * @return Globals
     */
    public static function instance()
    {
        global $container;
        return $container->getByType(self::class);
    }

    /**
     * Array with all parameters in config.neon
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Retrieves parameter from config.neon
     * @param string $path path/to/parameter
     * @return array|string|null
     */
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
     * Returns DAO Manager with repositories
     * @return \App\Model\DaoManager
     */
    public static function dao()
    {
        global $container;
        return $container->getService("dao");
    }

    /**
     * Generates link from everywhere
     *   If you wanna use it from console, make sure parameters/general/baseUrl is set
     * @param $destination
     * @param array $args
     * @return mixed
     */
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

    /**
     * Translates message from dictionary
     * @param string $message
     * @param int $count
     * @param array $parameters
     * @return mixed
     */
    public static function t($message, $count = null, $parameters = [])
    {
        $translator = self::getService(\Kdyby\Translation\Translator::class);
        return $translator->translate($message, $count, $parameters);
    }

    /**
     * Retrieves DI service by name or class
     * @param $name
     * @return null|object
     */
    public static function getService($name)
    {
        global $container;
        $service = $container->getByType($name, false);
        if (!$service) {
            $service = $container->getService($name);
        }
        return $service;
    }

    /**
     * Retrieves session
     * @return null|object
     */
    public static function getSession()
    {
        return self::getService('session');
    }
}