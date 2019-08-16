<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 02.02.2018
 * Time: 10:32
 */

namespace OnlineImperium\Manager;


use OnlineImperium\Globals;

class BaseManager
{
    use \Nette\SmartObject;

    /**
     * @var \App\Model\DaoManager
     */
    protected $dao;

    public function __construct()
    {
        $this->dao = Globals::dao();
    }

    protected function link($destination, $args = [])
    {
        return Globals::link($destination, $args);
    }

    protected function t($message, $count = null, $parameters = [])
    {
        return Globals::t($message, $count, $parameters);
    }
}