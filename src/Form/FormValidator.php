<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 26.10.2018
 * Time: 9:14
 */

namespace OnlineImperium\Form;


use Nette\Forms\Controls\TextBase;
use Nette\Forms\IControl;

class FormValidator
{
    const BANK_ACCOUNT = 'OnlineImperium\Form\FormValidator::validateBankAccount';
    const BANK_ACCOUNT_REGEX = '^(([0-9]{0,6})-)?([0-9]{2,10})\/[0-9]{4}$';

    public static function validateBankAccount(IControl $control)
    {
        assert($control instanceof TextBase);
        $value = $control->getValue();
        if (!preg_match('/^(([0-9]{0,6})-)?([0-9]{1,10})\/[0-9]{4}$/', $value, $matches)) {
            return FALSE;
        }
        $first = sprintf('%06d', $matches[1]);
        $second = sprintf('%010d', $matches[3]);

        // FIRST PART - MODULO 11
        $isOk = (10 * $first[0]
                + 5 * $first[1]
                + 8 * $first[2]
                + 4 * $first[3]
                + 2 * $first[4]
                + 1 * $first[5])
            % 11 == 0;

        if (!$isOk) {
            return false;
        }

        // SECOND PART - MODULO 11
        $isOk = (6 * $second[0]
                + 3 * $second[1]
                + 7 * $second[2]
                + 9 * $second[3]
                + 10 * $second[4]
                + 5 * $second[5]
                + 8 * $second[6]
                + 4 * $second[7]
                + 2 * $second[8]
                + 1 * $second[9])
            % 11 == 0;

        if (!$isOk) {
            return false;
        }

        return true;
    }
}