<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OnlineImperium\Tools;

/**
 * Description of FileTools
 *
 * @author Jan Stralka
 */
class FileTools
{

    const DIR_COUNTER = ".dir-counter.txt";
    /**
     * Recursive remove directory
     * @link http://stackoverflow.com/questions/3338123/how-do-i-recursively-delete-a-directory-and-its-entire-contents-files-sub-dir stackoverflow
     */
    public static function rrmdir($dir)
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);
            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (filetype($dir . "/" . $object) == "dir")
                    {
                        self::rrmdir($dir . "/" . $object);
                    }
                    else
                    {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function emptyDir($dir)
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);
            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (filetype($dir . "/" . $object) == "dir")
                    {
                        self::rrmdir($dir . "/" . $object);
                        //rmdir($dir . "/" . $object);
                    }
                    else
                    {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
        }
    }

    public static function dirFiles($dir)
    {
        $dir = array();
        $objects = scandir($dir);
        foreach ($objects as $object)
        {
            if ($object[0] != '.')
            {
                $dir[] = $object;
            }
        }
        return $dir;
    }
    
    public static function nextObjectName($dir, $suffix = "")
    {
        if (file_exists($dir . "/" . self::DIR_COUNTER))
        {
            $i = file_get_contents($dir . "/" . self::DIR_COUNTER);
            if (!is_numeric($i))
            {
                $i = 1;
            }
        }
        else
        {
            $i = 1;
        }
        for (; $i < 1000; $i++)
        {
            if (!file_exists($dir . "/" . $i . $suffix))
            {
                file_put_contents($dir . "/" . self::DIR_COUNTER, $i+1);
                return $i;
            }
        }
        throw new \Exception("Nepodarilo se najit nazev pro dalsi polozku v adresari.");
    }

}
