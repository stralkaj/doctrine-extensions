<?php
/**
 * Created by PhpStorm.
 * User: neime
 * Date: 05.03.2019
 * Time: 11:02
 */

namespace OnlineImperium\DI;


use Kdyby\Doctrine\DI\IEntityProvider;
use Kdyby\Translation\DI\ITranslationProvider;
use Nette\DI\CompilerExtension;
use Nette\Forms\Form;
use Nette\Forms\Validator;

class NetteExtensionsExtension extends CompilerExtension implements ITranslationProvider
{
    private $defaults = [
    ];

    public function loadConfiguration()
    {
        //$this->setupFormErrorsTranslations();
    }

    /**
     * @return array|string[]
     */
    public function getTranslationResources(): array
    {
        return [
            __DIR__ . '/../lang'
        ];
    }

    public function afterCompile(\Nette\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBody('\OnlineImperium\DI\NetteExtensionsExtension::setupFormErrorsTranslations();');
    }

    public static function setupFormErrorsTranslations()
    {
        $translations = [
            Form::PROTECTION => 'protection',
            Form::EQUAL => 'equal',
            Form::NOT_EQUAL => 'notEqual',
            Form::FILLED => 'filled',
            Form::BLANK => 'blank',
            Form::MIN_LENGTH => 'minLength',
            Form::MAX_LENGTH => 'maxLength',
            Form::LENGTH => 'length',
            Form::EMAIL => 'email',
            Form::URL => 'url',
            Form::INTEGER => 'integer',
            Form::FLOAT => 'float',
            Form::MIN => 'min',
            Form::MAX => 'max',
            Form::RANGE => 'range',
            Form::MAX_FILE_SIZE => 'maxFileSize',
            Form::MAX_POST_SIZE => 'maxPostSize',
            Form::MIME_TYPE => 'mimeType',
            Form::IMAGE => 'image',
            \Nette\Forms\Controls\SelectBox::VALID => 'selectBoxValid',
            \Nette\Forms\Controls\UploadControl::VALID => 'uploadControlValid',
        ];
        foreach ($translations as $key => $val) {
            Validator::$messages[$key] = "form.error.$val";
        }
    }
}