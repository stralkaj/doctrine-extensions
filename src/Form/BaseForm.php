<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 11.10.2018
 * Time: 14:14
 */

namespace OnlineImperium\Form;


use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;
use Nette\SmartObject;
use OnlineImperium\Globals;

abstract class BaseForm
{
    use SmartObject;

    /**
     * @var \Nette\Application\UI\Presenter
     */
    protected $presenter;

    /**
     * @var \App\Model\DaoManager
     */
    protected $dao;

    public function __construct()
    {
        $this->dao = Globals::dao();
    }


    public final function create()
    {
        $form = $this->newForm();
        $this->init($form);
        return $form;
    }

    protected abstract function init(Form $form);

    public abstract function success(Form $form);


    public function attached(Form $form)
    {
    }

    public function validate(Form $form)
    {
    }

    public function submit(Form $form)
    {
    }

    /**
     * @return Form
     * Docasne prepsano na private - aby se nepouzivalo ve starych subclass
     */
    private function newForm($addProtection = true)
    {
        $form = new Form();
        if ($addProtection) {
            $form->addProtection();
        }
        $form->onValidate[] = [$this, 'validate'];
        $form->onSubmit[] = [$this, 'submit'];
        $form->onSuccess[] = [$this, 'success'];
        $form->onAnchor[] = function (Form $attachedForm) {
            $this->presenter = $attachedForm->getPresenter();
        };
        $form->onAnchor[] = [$this, 'attached'];
        if (class_exists('Kdyby\Translation\Translator')) {
            /** @var Translator $translator */
            $translator = Globals::getService(\Kdyby\Translation\Translator::class);
            if ($translator) {
                //$domain = $translator->domain('form');
                $form->setTranslator($translator);
            }
        }
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