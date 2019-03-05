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

class NetteExtensionsExtension extends CompilerExtension implements ITranslationProvider
{
    private $defaults = [
    ];

    /**
     * @return array|string[]
     */
    public function getTranslationResources(): array
    {
        return [
            __DIR__ . '/../lang'
        ];
    }
}