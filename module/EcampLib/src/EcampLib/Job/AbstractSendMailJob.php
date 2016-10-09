<?php

namespace EcampLib\Job;

use Zend\Mail\Message;
use Zend\Mail\Transport\Factory;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\ApplicationInterface;
use Zend\View\Model\ViewModel;

abstract class AbstractSendMailJob extends AbstractJobBase
{
    /** @var TransportInterface */
    private $mailTransport;

    public function doInit(ApplicationInterface $app)
    {
        parent::doInit($app);

        $config = $app->getServiceManager()->get('Config');
        $transportConfig = $config['mail']['transport'] ?: array();
        $this->mailTransport = Factory::create($transportConfig);
    }



    protected function createHtmlPart(ViewModel $viewModel)
    {
        return $this->createPart($viewModel, Mime::TYPE_HTML);
    }

    protected function createTextPart(ViewModel $viewModel)
    {
        return $this->createPart($viewModel, Mime::TYPE_TEXT);
    }

    protected function createMultipart(array $parts)
    {
        $partsMessage = new MimeMessage();
        $partsMessage->setParts($parts);

        $contentPart = new MimePart($partsMessage->generateMessage());
        $contentPart->type = Mime::MULTIPART_ALTERNATIVE;
        $contentPart->boundary = $partsMessage->getMime()->boundary();

        return $contentPart;
    }

    protected function createPart(ViewModel $viewModel, $type)
    {
        $content = $this->render($viewModel);
        $part = new MimePart($content);
        $part->encoding = Mime::ENCODING_8BIT;
        $part->type = $type;
        $part->charset = 'utf-8';

        return $part;
    }


    protected function createFlagAttachment()
    {
        $attachment = new MimePart(
            file_get_contents(__MODULE__ . '/EcampCore/assets/img/fa-flag.png'));
        $attachment->id = 'fa_flag';
        $attachment->filename = 'fa_flag.png';
        $attachment->type = Mime::TYPE_OCTETSTREAM;
        $attachment->encoding = Mime::ENCODING_BASE64;
        $attachment->disposition = Mime::DISPOSITION_INLINE; // Mime::DISPOSITION_ATTACHMENT;

        return $attachment;
    }

    protected function createMailBgAttachment()
    {
        $attachment = new MimePart(
            file_get_contents(__MODULE__ . '/EcampCore/assets/img/mail-bg.png'));
        $attachment->id = 'mail_bg';
        $attachment->filename = 'mail_bg.png';
        $attachment->type = Mime::TYPE_OCTETSTREAM;
        $attachment->encoding = Mime::ENCODING_BASE64;
        $attachment->disposition = Mime::DISPOSITION_INLINE; // Mime::DISPOSITION_ATTACHMENT;

        return $attachment;
    }


    /**
     * @param  ViewModel $viewModel
     * @return string
     */
    protected function render(ViewModel $viewModel)
    {
        $viewModel->setOption('has_parent', true);

        /** @noinspection PhpVoidFunctionResultUsedInspection */

        return $this->view->render($viewModel);
    }

    protected function sendMail(Message $message)
    {
        $this->mailTransport->send($message);
    }
}
