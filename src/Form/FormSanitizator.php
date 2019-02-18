<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 23.01.2019
 * Time: 14:43
 */

namespace OnlineImperium\Form;


class FormSanitizator
{
    public static function sanitizePhone($value)
    {
        return str_replace([' ', '-'], '', $value);
    }

    public static function sanitizePsc($value)
    {
        return str_replace([' ', '-'], '', $value);
    }

    public static function sanitizeBirthNumber($value)
    {
        $result = str_replace([' ', '/'], '', $value);
        return preg_replace('/(\d{4})$/', '/$1', $result);
    }

    public static function sanitizeBankAccount($value)
    {
        return str_replace([' '], '', $value);
    }
}