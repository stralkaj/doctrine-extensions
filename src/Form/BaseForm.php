<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 11.10.2018
 * Time: 14:14
 */

namespace OnlineImperium\Form;


use App\Model\DaoManager;
use App\Model\Globals;
use Nette\Application\UI\Form;
use Nette\SmartObject;

abstract class BaseForm
{
    use SmartObject;

    protected $presenter;

    protected $name;

    /**
     * @var DaoManager
     */
    protected $dao;

    public function __construct(\Nette\Application\UI\Presenter $presenter, $name)
    {
        $this->presenter = $presenter;
        $this->name = $name;
        $this->dao = Globals::dao();
    }


    public abstract function create();

    public abstract function success(Form $form);

    public function validate(Form $form)
    {
    }

    public function submit(Form $form)
    {
    }

    /**
     * @return Form
     */
    protected function newForm($addProtection = true)
    {
        $form = new Form($this->presenter, $this->name);
        if ($addProtection) {
            $form->addProtection();
        }
        $form->onValidate[] = [$this, 'validate'];
        $form->onSubmit[] = [$this, 'submit'];
        $form->onSuccess[] = [$this, 'success'];
        return $form;
    }

    // Metody z presenteru:

    protected function link($destination, $args = [])
    {
        return $this->presenter->link($destination, $args);
    }

    protected function t($message, $count = null, $parameters = [])
    {
        return Globals::t($message, $count, $parameters);
    }

    protected function flashMessage($message, $type = 'info')
    {
        return $this->presenter->flashMessage($message, $type);
    }

    protected function redirect($destination, $args = [])
    {
        $this->presenter->redirect($destination, $args);
    }
}