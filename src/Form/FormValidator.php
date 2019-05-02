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
    // Validation callbacks:
    const BANK_ACCOUNT = 'OnlineImperium\Form\FormValidator::validateBankAccount';
    const BIRTH_NUMBER = 'OnlineImperium\Form\FormValidator::validateBirthNumber';

    // Regexs:
    const BANK_ACCOUNT_REGEX = '^(([0-9]{0,6})-)?([0-9]{2,10})\/[0-9]{4}$';
    const PHONE_REGEX = "^(\\+?[0-9]{3,5})?[ -]?[0-9]{3}[ -]?[0-9]{3}[ -]?[0-9]{3}\$"; // Je potreba sanitizovat!
    const STREET_AND_NUMBER_REGEX = "^(.+[ ]+)?[0-9]+[a-z]?(/[0-9]+[a-z]?)?\$";
    const PSC_REGEX = "^[0-9]{3} ?[0-9]{2}\$";
    const DATE_REGEX = "^([1-9]|[1-2][0-9]|3[0-1])\\. ?([1-9]|1[0-2])\\. ?(19|20)[0-9][0-9]\$";
    const PERSON_NAME_REGEX = '^[^ ]{2,}( +[^ ]{2,})+$'; // Jednoducha validace jmena, ktere se sklada z 2 a vice slov o 2 a vice znacich
    const ICO_REGEX = '^[0-9]{8}$';
    const DIC_REGEX = '^(|[A-Z]{2}[0-9A-Z]{2,12})$';
	const BIRTH_NUMBER_REGEX = '^(\d\d)(\d\d)(\d\d) */? *(\d\d\d)(\d?)$';
	const PASSWORD_REGEX = '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$';

	// Messages:
    /** @deprecated  */
    const REQUIRED_MSG = 'form.error.required';
    /** @deprecated  */
    const EMAIL_MSG = 'form.error.email';

	const PASSWORD_MSG = 'form.error.password';
	const PASSWORD_AGAIN_MSG = 'form.error.passwordAgain';
	const CAPTCHA_MSG = 'form.error.captcha';
	const BANK_ACCOUNT_MSG = 'form.error.bankAccount';


	public static function validateBankAccount(IControl $control)
    {
/*         assert($control instanceof TextBase);
        $value = $control->getValue();
        if (!preg_match('/' . self::BANK_ACCOUNT_REGEX . '/', $value, $matches)) {
            return false;
        }
<<<<<<< HEAD
        //return true;
        //TODO vyresit problem s velkymi cisly
        $first = str_pad($matches[1], 6, '0', STR_PAD_LEFT); //sprintf('%06d', $matches[1]);
        $second = str_pad($matches[3], 10, '0', STR_PAD_LEFT); //sprintf('%010d', $matches[3]);
        //bdump("First: $first, second: $second"); //TODO vyresit problem s pretekanim
=======
        $first = sprintf('%06d', $matches[1]);
        $second = sprintf('%010d', $matches[3]);
        bdump("First: $first, second: $second"); //TODO vyresit problem s pretekanim
>>>>>>> 64c176256a4b41ec4e5f63d0c0ada36129b09f4b

        // FIRST PART - MODULO 11
        $v = gmp_add(10 * $first[0], 5 * $first[1]);
        $v = gmp_add($v, 8 * $first[2]);
        $v = gmp_add($v, 4 * $first[3]);
        $v = gmp_add($v, 2 * $first[4]);
        $v = gmp_add($v, 1 * $first[5]);
        $isOk = (gmp_intval(gmp_mod($v, 11)) == 0);
        if (!$isOk) {
            return false;
        }

        // SECOND PART - MODULO 11
        $v = gmp_add(6 * $second[0], 3 * $second[1]);
        $v = gmp_add($v, 7 * $second[2]);
        $v = gmp_add($v, 9 * $second[3]);
        $v = gmp_add($v, 10 * $second[4]);
        $v = gmp_add($v, 5 * $second[5]);
        $v = gmp_add($v, 8 * $second[6]);
        $v = gmp_add($v, 4 * $second[7]);
        $v = gmp_add($v, 2 * $second[8]);
        $v = gmp_add($v, 1 * $second[9]);
        $isOk = (gmp_intval(gmp_mod($v, 11)) == 0);

        if (!$isOk) {
            return false;
        } */

        return true;
    }

    public static function validateBirthNumber(IControl $control)
    {
		$rc = $control->getValue();

		// be liberal in what you receive
		if (!preg_match('#^' . self::BIRTH_NUMBER_REGEX . '$#', $rc, $matches)) {
			return false;
		}

		list(, $year, $month, $day, $ext, $c) = $matches;

		if ($c === '') {
			$year += $year < 54 ? 1900 : 1800;
		} else {
			// check number mod 11
			$mod = ($year . $month . $day . $ext) % 11;
			if ($mod === 10) $mod = 0;
			if ($mod !== (int) $c) {
				return false;
			}

			$year += $year < 54 ? 2000 : 1900;
		}

		// month gender correction
		if ($month > 70 && $year > 2003) {
			$month -= 70;
		} elseif ($month > 50) {
			$month -= 50;
		} elseif ($month > 20 && $year > 2003) {
			$month -= 20;
		}

		if (!checkdate($month, $day, $year)) {
			return false;
		}

		return true;
    }
}