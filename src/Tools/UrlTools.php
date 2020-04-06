<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 18.11.2019
 * Time: 14:24
 */

namespace OnlineImperium\Tools;


class UrlTools
{
    public static function trimQuery($url)
    {
        $trimmed = preg_replace('/\?[^#]*/', '', $url);
        return $trimmed;
    }

    public static function trimPath($url)
    {
        if (preg_match('/^([a-z]+:\/\/[^\/]+)/i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public static function getDomain($url, $trimWww = true)
    {
        $domain = parse_url($url, PHP_URL_HOST);
        $domain = strtolower($domain);
        if ($domain === false) {
            return null;
        }
        if ($trimWww) {
            $domain = preg_replace('/^www\./', '', $domain);
        }
        return $domain;
    }

    public static function trimTld($domain)
    {
        return preg_replace('/\.(co\.uk|[a-z]+)$/', '', $domain);
    }

    public static function getFinalUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);

        $result = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        return $result;
    }
}