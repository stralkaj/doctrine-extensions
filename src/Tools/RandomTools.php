<?php

namespace OnlineImperium\Tools;

use Nette;

/**
 * Nastroje pro vytvareni nahodnych prvku.
 * @author Jan Stralka
 */
class RandomTools
{

    private static $alnum = "abcdefghijklmnopqrstuvwxyz0123456789";

    public static function getString($length)
    {
        $str = "";
        for ($i = 0; $i < $length; $i++)
        {
            $str .= self::$alnum[rand(0, strlen(self::$alnum) - 1)];
        }
        return $str;
    }

}
