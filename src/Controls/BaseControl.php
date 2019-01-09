<?php

namespace OnlineImperium\Controls;

/**
 * Base Control
 */
use OnlineImperium\TemplateExtensions\TemplateFilters;
use Nette\Application\UI\Control;

class BaseControl extends Control
{
    protected function createTemplate()
    {
        $template = parent::createTemplate();
        TemplateFilters::setupTemplateFilters($template);
        return $template;
    }
}
