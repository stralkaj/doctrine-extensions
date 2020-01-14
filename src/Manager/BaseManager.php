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

    /**
     * @var \Kdyby\Translation\Translator
     */
    protected $translator;

    public function __construct()
    {
        $this->dao = Globals::dao();
        $this->translator = Globals::getService(\Kdyby\Translation\Translator::class);
    }

    protected function link($destination, $args = [])
    {
        return Globals::link($destination, $args);
    }

    protected function t($message, $count = null, $parameters = [])
    {
        return $this->translator->translate($message, $count, $parameters);
    }
}