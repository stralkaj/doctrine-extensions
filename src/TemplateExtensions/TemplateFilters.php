<?php

namespace OnlineImperium\TemplateExtensions;

use App\Model\Entity\Coin;
use App\Model\type;
use Nette;
use OnlineImperium\Globals;

/**
 * Sada filtru pro latte sablony
 * @author Jan Stralka
 */
class TemplateFilters
{

    use \Nette\SmartObject;
    public static function setupTemplateFilters($template)
    {
        $className = self::class;
        $template->addFilter('price', "$className::price");
        $template->addFilter('datetime', "$className::datetime");
        $template->addFilter('usertext', "$className::userText");
        $template->addFilter('br', "$className::br");
        $template->addFilter('unitcount', "$className::unitCount");
        $template->addFilter('encodeEmail', "$className::encodeEmail");
        $template->addFilter('nohttp', "$className::nohttp");
        $template->addFilter('rating2stars', "$className::rating2stars");
        $template->addFilter('printArray', "$className::printArray");
        $template->addFilter('yesno', "$className::yesOrNo");
        $template->addFilter('YesNo', "$className::yesOrNoUpper");
        $template->addFilter('percent', "$className::percent");
        $template->addFilter('spacemb', "$className::spaceMB");
        $template->addFilter('rank2word', "$className::rank2word");
        $template->addFilter('decimal', "$className::decimal");
        $template->addFilter('money', "$className::money");
        $template->addFilter('round', "round");
        $template->addFilter('roundUp', "ceil");
        $template->addFilter('roundDown', "floor");
        $template->addFilter("b64encode", "base64_encode");

        $template->addFilter("companyImage", "$className::filterCompanyImage");

        if ($template instanceof Nette\Bridges\ApplicationLatte\Template) {
            $vars = self::getDefaultVars();
            foreach ($vars as $name=>$val) {
                $template->{$name} = $val;
            }
        }
    }

    public static function getDefaultVars()
    {
        return [
            'formatDateTime' => Globals::t('settings.date.formatDateTime'),
            'formatDateTimeSeconds' => Globals::t('settings.date.formatDateTimeSeconds'),
            'formatDate' => Globals::t('settings.date.formatDate'),
            'formatTime' => Globals::t('settings.date.formatTime')
        ];
    }
    
    
    public static function price($price, $showCents = true)
    {
        $decimals = (!$showCents || $price == ((int) $price)) ? 0 : 2;
        return number_format($price, $decimals, ",", " ");//str_replace(".", ",", round($price, 2));
    }
    
    public static function datetime(Nette\Utils\DateTime $date)
    {
        if (!$date)
        {
            return "";
        }
        return $date->format("j.n.Y G:i");
    }
    
    public static function userText($text)
    {
        $text = str_replace("\r", "", $text);
        $paragraphs = explode("\n\n", $text);
        $result = "";
        foreach ($paragraphs as $p)
        {
            $p = htmlspecialchars($p);
            $p = str_replace("\n", "<br />", $p);
            $result .= "<p>" . $p . "</p>";
        }
        return $result;
    }
    
    public static function br($text)
    {
        $text = trim(str_replace("\r", "", $text));
        $text = htmlspecialchars($text);
        $text = str_replace("\n", "<br />", $text);
        return $text;
    }
    
    public static function unitCount($count, $singular, $plural2, $plural)
    {
        
        if ($count == 1)
        {
            return $count . " " . $singular;
        }
        if ($count <= 4)
        {
            return $count . " " . $plural2;
        }
        
        return $count . " " . $plural;
    }
    
    /**
     * Encodes e-mail to robot protected string
     *   Decode function in JS is defined in main.js
     * @param type $email
     * @return type
     */
    public static function encodeEmail($email)
    {
        $encoded = str_replace("@", "#", $email);
        $encoded = strrev($encoded);
        $encoded = base64_encode($encoded);
        $encoded = strrev($encoded);
        return $encoded;
    }
    
    public static function nohttp($link)
    {
        if ($link == '')
        {
            return '';
        }
        $result = strstr($link, "://");
        if ($result === false)
        {
            return $link;
        }
        return substr($result, 3);
    }
    
    public static function printArray($arr, $separator, $property = null, $empty = "")
    {
        $len = count($arr);
        if (!$len)
        {
            return $empty;
        }
        $result = "";
        $i = 0;
        foreach ($arr as $item)
        {
            if ($i > 0)
            {
                $result .= $separator;
            }
            if ($property)
            {
                $result .= $item->$property;
            }
            else
            {
                $result .= $item;
            }
            $i++;
        }
        return $result;
    }
    
    public static function yesOrNo($value)
    {
        if ($value === null)
        {
            return "-";
        }
        if ($value)
        {
            return "ano";
        }
        else
        {
            return "ne";
        }
    }

    public static function yesOrNoUpper($value)
    {
        return ucfirst(self::yesOrNo($value));
    }
    
    public static function percent($value)
    {
        if ($value === null)
        {
            return "-";
        }
        
        return str_replace(".", ",", round($value, 4)) . " %";
    }
    
    public static function spaceMB($space,$unlimited='neomezený')
    {
        barDump($unlimited);
        if ($space === null) {
            return "-";
        }
        if ($space == -1) {
            return $unlimited;
        }
        if ($space >= 1E6) {
            return round($space / 1E6, 1) . " TB";
        } else if ($space >= 1000) {
            return round($space / 1000, 1) . " GB";
        }

        return $space . " MB";
    }
    
    public static function rating2stars($ratingDouble)
    {
        if ($ratingDouble === null)
        {
            return "&nbsp;";
        }
        $rating = 5 * $ratingDouble;
        $result = "";
        $stars1 = (int) $rating;
        for ($i = 0; $i < $stars1; $i++)
        {
            $result .= "<i class=\"fa fa-star\"></i> ";
        }
        
        $remainder = ($rating - $stars1);
        $rem2 = round(2 * $remainder);
        switch ($rem2)
        {
            case 0:
                $result .= "<i class=\"fa fa-star-o\"></i> ";
                break;
            case 1:
                $result .= "<i class=\"fa fa-star-half-o\"></i> ";
                break;
            case 2:
                $result .= "<i class=\"fa fa-star\"></i> ";
                break;
        }
        $stars2 = 4 - $stars1;
        for ($i = 0; $i < $stars2; $i++)
        {
            $result .= "<i class=\"fa fa-star-o\"></i> ";
        }
        
        return $result;
    }
    
    public static function rank2word($rank)
    {
        if ($rank < 20)
        {
            return "Špatné";
        }
        if ($rank < 40)
        {
            return "Podprůměrné";
        }
        if ($rank < 60)
        {
            return "Průměrné";
        }
        if ($rank < 80)
        {
            return "Nadprůměrné";
        }
        return "Výborné";
    }

    public static function filterCompanyImage($companyId)
    {
        return "img/company/" . $companyId . "/logo.jpg";
    }

    public static function decimal($input, $decimals = null, $decimalPoint = ',', $thousands = ' ')
    {
        if (!is_numeric($input)) {
            return $input;
        }
        $input = rtrim(sprintf('%F', $input), '0');
        if ($decimals === null) {
            if (preg_match('/\.([0-9]*)$/', $input, $matches)) {
                $decimals = strlen($matches[1]);
            } else {
                $decimals = 0;
            }
        }
        barDump($input);
        $result = number_format($input, $decimals, $decimalPoint, $thousands);
        return $result;
    }

    public static function money($input, $coin, $decimals = null)
    {
        if (!$coin) {
            return self::decimal($input, null);
        }
        if ($decimals === null) {
            // U krypta se nebudou vypisovat vsechny nuly na konci
            if ($coin->isCrypto) {
                $decimals = null;
            } else {
                $decimals = ($input != round($input)) ? $coin->decimals : 0;
            }

        }
        $number = round($input, $coin->decimals);
        $result = self::decimal($number, $decimals);
        if ($coin->prefix) {
            $result = $coin->prefix . ' ' . $result;
        }
        if ($coin->postfix) {
            $result = $result . ' ' . $coin->postfix;
        }
        if (!$coin->prefix && !$coin->postfix) {
            $result = $result . ' ' . $coin->symbol;
        }
        return $result;
    }
}
