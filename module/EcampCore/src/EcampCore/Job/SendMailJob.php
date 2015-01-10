<?php

namespace EcampCore\Job;

use EcampLib\Job\AbstractBootstrappedJobBase;
use Zend\Mail\Message;
use Zend\Mail\Transport\Factory;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\View\Model\ViewModel;

abstract class SendMailJob extends AbstractBootstrappedJobBase
{
    /**
     * @return \Zend\View\Renderer\RendererInterface
     */
    private function getViewRenderer()
    {
        return $this->getServiceLocator()->get('ZfcTwigRenderer');
    }

    /**
     * @return \Zend\Mail\Transport\TransportInterface
     */
    private function getMailTransport()
    {
        $config = $this->getServiceLocator()->get('Config');
        $transportConfig = $config['mail']['transport'] ?: array();

        return Factory::create($transportConfig);
    }

    protected function renderViewModel(ViewModel $viewModel) {
        return $this->getViewRenderer()->render($viewModel);
    }


    protected function createHtmlPartByViewModel(ViewModel $viewModel) {
        return $this->createPartByViewModel($viewModel, 'text/html');
    }

    protected function createTextPartByViewModel(ViewModel $viewModel) {
        return $this->createPartByViewModel($viewModel, 'text/plain');
    }

    protected function createPartByViewModel(ViewModel $viewModel, $type) {
        $content = $this->renderViewModel($viewModel);
        $part = new MimePart($content);
        $part->encoding = Mime::ENCODING_8BIT;
        $part->type = $type;
        $part->charset = 'utf-8';

        return $part;
    }

    public function perform()
    {
        $mailMessage = new Message();
        $mailMessage->setFrom('ecamp-test@musegg.ch');

        $parts = array_filter(array(
            $this->createTextPart(),
            $this->createHtmlPart()
        ));

        $body = new MimeMessage();

        if(count($parts) > 0){
            $contentPart = null;

            if(count($parts) == 1) {
                $contentPart = array_values($parts)[0];
            }

            elseif(count($parts) > 1) {
                $mailContent = new MimeMessage();
                $mailContent->setParts($parts);

                $contentPart = new MimePart($mailContent->generateMessage());
                $contentPart->type = "multipart/alternative;" . "\n" .
                    "boundary=\"" . $mailContent->getMime()->boundary() . "\"";
            }

            $body->addPart($contentPart);
            $body->addPart($this->createFlagAttachment());
            $body->addPart($this->createMailBgAttachment());
        }

        $mailMessage->setBody($body);
        $mailMessage->setEncoding('UTF-8');

        /** @var \Zend\Mail\Header\ContentType $contentType */
        $contentType = $mailMessage->getHeaders()->get('content-type');
        $contentType->setType('multipart/related');

        $this->completeMail($mailMessage);
        $this->sendMail($mailMessage);
    }

    protected function sendMail(Message $message)
    {
        $trsp = $this->getMailTransport();
        $trsp->send($message);
    }

    abstract public function createTextPart();
    abstract public function createHtmlPart();
    abstract public function completeMail(Message $mail);


    protected function createFlagAttachment()
    {
        $attachment = new MimePart(
            file_get_contents(__MODULE__ . '/EcampCore/assets/img/fa-flag.png'));
        $attachment->id = 'fa_flag';
        $attachment->filename = 'fa_flag.png';
        $attachment->type = Mime::TYPE_OCTETSTREAM;
        $attachment->encoding = Mime::ENCODING_BASE64;
        $attachment->disposition = Mime::DISPOSITION_INLINE; //DISPOSITION_ATTACHMENT;

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
        $attachment->disposition = Mime::DISPOSITION_INLINE; //DISPOSITION_ATTACHMENT;

        return $attachment;
    }
}
