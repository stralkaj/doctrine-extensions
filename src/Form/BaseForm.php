<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 11.10.2018
 * Time: 14:14
 */

namespace OnlineImperium\Form;


use Nette\Application\UI\Form;
use Nette\SmartObject;
use OnlineImperium\Globals;

abstract class BaseForm
{
    use SmartObject;

    /**
     * @var \Nette\Application\UI\Control
     */
    protected $parent;

    /**
     * @var \Nette\Application\UI\Presenter
     */
    protected $presenter;

    protected $name;

    /**
     * @var \App\Model\DaoManager
     */
    protected $dao;

    public function __construct(\Nette\Application\UI\Control $control, $name)
    {
        $this->parent = $control;
        $this->presenter = $control->getPresenter();
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
        $form = new Form($this->parent, $this->name);
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