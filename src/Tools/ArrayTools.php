<?php
/**
 * Created by PhpStorm.
 * User: Honza
 * Date: 05.05.2017
 * Time: 12:07
 */

namespace OnlineImperium\Tools;

class ArrayTools
{

    public static function removeItemByValue(&$array, $value)
    {
        if (($key = array_search($value, $array)) !== false)
        {
            // Odebere ID kategorie, aby se neduplikovaly zaznamy
            unset($array[$key]);
        }
    }

    public static function objectToArray($object, $skipNull = false)
    {
        $array = (array) $object;
        if ($skipNull) {
            self::skipNullItems($array);
        }
        return $array;
    }

    public static function firstItem($array)
    {
        if ($array) {
            if (!is_array($array)) {
                throw new \Exception('Argument is not array');
            }
            foreach ($array as $item) {
                return $item;
            }
        }
        return null;
    }

    public static function skipNullItems(&$array)
    {
        $array = array_filter($array, function ($item) {
            return !is_null($item);
        });
        $delete = [];
        foreach ($array as $key => &$item) {
            if (is_array($item)) {
                $item = self::skipNullItems($item);
                if (!$item) {
                    $delete[] = $key;
                }
            }
        }

        foreach ($delete as $key) {
            unset($array[$key]);
        }
        return $array;
    }
}