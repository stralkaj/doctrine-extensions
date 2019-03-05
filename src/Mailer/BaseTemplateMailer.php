<?php

namespace OnlineImperium\Mailer;

use Nette;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Mail\SmtpMailer;
use Nette\Utils\Strings;
use OnlineImperium\Globals;
use OnlineImperium\TemplateExtensions\TemplateFilters;
use OnlineImperium\Tools\TextTools;

/**
 * Vytvori HTML e-mail z latte sablony a odesle jej.
 * @author Jan Stralka
 * @link https://phpfashion.com/generovani-odkazu-kuprikladu-v-emailech-a-nette
 * @property string $templateName
 */
class BaseTemplateMailer
{
    use \Nette\SmartObject;

    protected $templatesPath;

    protected $layoutPath;

    protected $headerTemplate = "_header.latte";
    protected $footerTemplate = "_footer.latte";

    public $params = [];

    /** @var string */
    public $sender;

    /** @var string */
    public $subject;
    
    /** @var string */
    public $headline;
    
    /** @var string */
    public $replyTo;

    /** @var string */
    public $templateName;

    protected $sendingEnabled;

    public function __construct($templateName)
    {
        $this->sendingEnabled = Globals::getParameter('mailer/enabled');
        $this->sender = Globals::getParameter('mailer/sender');
        $this->templateName = $templateName;
    }

    public function __set($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @param string|array $to
     * @param string $from
     */
    public function send($to)
    {
        if ($to === null || $to === '') {
            return;
        }

        if (!is_array($to)) {
            $to = [$to];
        }

        $this->setupParameters();

        // https://phpfashion.com/generovani-odkazu-kuprikladu-v-emailech-a-nette


        //$latte = $latteFactory->create();
        //$template = $this->templateFactory->createTemplate();
        /** @var ILatteFactory $latteFactory */
        $latteFactory = Globals::getService(ILatteFactory::class);
        $latte = $latteFactory->create();//$template->getLatte();

        \Nette\Bridges\ApplicationLatte\UIMacros::install($latte->getCompiler());
        $latte->addProvider('uiControl', Globals::getService(LinkGenerator::class));
        TemplateFilters::setupTemplateFilters($latte);
        $allParams = $this->params + TemplateFilters::getDefaultVars();
        $layoutPath = $this->layoutPath ?? $this->templatesPath . "/_layout.latte";
        $html = $latte->renderToString($layoutPath, $allParams);

        $mail = new Message();
        $mail->setFrom($this->sender)
            ->setEncoding('utf-8')
            //->setContentType('multipart/alternative', 'utf-8')
            ->setSubject($this->subject)
            ->setHtmlBody($html, ($this->sendingEnabled) ? $this->templatesPath : false);

        foreach ($to as $oneTo) {
            $mail->addTo($oneTo);
        }
        
        if ($this->replyTo)
        {
            $mail->setHeader("Reply-To", $this->replyTo);
        }
        
        $mailer = new SendmailMailer();

        if (Globals::getParameter('mailer/archive')) {
            $this->archiveMail($mail, $to);
        }

        $enabled = $this->sendingEnabled;
        if ($enabled === false) {
            return;
        }

        $mailer->send($mail); //SendMail::send is void
    }

    protected function setupParameters()
    {
        $p = &$this->params;
        $this->params['header_file'] = $this->headerTemplate;
        $this->params['footer_file'] = $this->footerTemplate;
        $this->params['content_file'] = realpath($this->templatesPath . '/'. $this->templateName . '.latte');
        $p['subject'] = $this->subject;
        $p['headline'] = ($this->headline) ? $this->headline : $this->subject;

        $p['formatDateTime'] = Globals::t('settings.date.formatDateTime');
        $p['formatDateTimeSeconds'] = Globals::t('settings.date.formatDateTimeSeconds');
        $p['formatDate'] = Globals::t('settings.date.formatDate');
        $p['formatTime'] = Globals::t('settings.date.formatTime');
    }

    protected function archiveMail(Message $mail, $to)
    {
        $mailDir = NETTE_ROOT . '/log/mail-archive';
        if (!file_exists($mailDir)) {
            mkdir($mailDir);
        }

        $toStr = Strings::webalize(implode('-', $to));

        $fileName = date('Ymd-His') . '-' . $this->templateName . '-' . $toStr . '.html';
        $filePath = $mailDir . '/' . $fileName;
        file_put_contents($filePath, $mail->getHtmlBody());
    }
}
