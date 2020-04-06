<?php
/**
 * Created by PhpStorm.
 * User: Honza
 * Date: 27.11.2017
 * Time: 22:31
 */

namespace OnlineImperium\Tools;


use OnlineImperium\Globals;

class DateTools
{
    public static function getMonthName($month = null)
    {
        if (is_null($month)) {
            $month = date('n');
        }
        $index = $month - 1;
        $monthNames = explode('|', Globals::t('settings.general.months'));
        return $monthNames[$index];
    }

    public static function dateFromInput($input) : ?\DateTime
    {
        if (!$input) {
            return null;
        }
        return \DateTime::createFromFormat("Y-m-d H:i:s", $input . " 00:00:00");
    }
}