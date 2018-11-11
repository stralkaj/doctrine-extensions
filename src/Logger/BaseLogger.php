<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 11.11.2018
 * Time: 9:51
 */

namespace OnlineImperium\Logger;


abstract class BaseLogger
{
    public abstract function logFileName();

    protected function getLogFullFileName()
    {
        return NETTE_ROOT . '/log/' . $this->logFileName();
    }

    protected function logToFile($row)
    {
        $row = "[" . date('Y-m-d H:i:s') . "] " . $row . "\r\n";
        file_put_contents($this->getLogFullFileName(), $row, FILE_APPEND);
    }

}