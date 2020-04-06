<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 03.10.2017
 * Time: 17:21
 */

namespace OnlineImperium\Tools;


use Nette\Application\UI\Form;
use Nette\Forms\Controls\Checkbox;

class FormTools
{
    public static function setDefaultsScalars(Form $form, $values)
    {
        foreach ($values as $key => $item) {
            if (isset($form[$key])) {
                if (is_null($item) || is_scalar($item) || is_array($item)) {
                    if ($form[$key] instanceof Checkbox) {
                        $form[$key]->setDefaultValue((boolean) $item);
                    } else {
                        $form[$key]->setDefaultValue($item);
                    }
                }
            }
        }
    }
}