<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 15.01.2019
 * Time: 10:01
 */

namespace OnlineImperium\Base;

use ReflectionClass;

class BaseEnum
{
    public static function getArray()
    {
        $refl = new ReflectionClass(get_called_class());
        $constants = $refl->getConstants();
        $flipped = array_flip($constants);
        return $flipped;
    }

}