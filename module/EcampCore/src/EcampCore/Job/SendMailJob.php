<?php

namespace EcampCore\Job;

use EcampLib\Job\AbstractBootstrappedJobBase;
use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\View\Model\ViewModel;

abstract class SendMailJob extends AbstractBootstrappedJobBase
{
    /**
     * @return \Zend\View\Renderer\RendererInterface
     */
    private function getViewRenderer()
    {
        return $this->getServiceLocator()->get('ViewRenderer');
    }

    /**
     * @return \Zend\Mail\Transport\TransportInterface
     */
    private function getMailTransport()
    {
        return new \Zend\Mail\Transport\Sendmail();
    }

    protected function renderViewModel(ViewModel $viewModel)
    {
        return $this->getViewRenderer()->render($viewModel);
    }

    protected function createHtmlPart(ViewModel $viewModel)
    {
        $html = $this->renderViewModel($viewModel);
        $htmlPart = new MimePart($html);
        $htmlPart->type = 'text/html';
        $htmlPart->charset = 'utf-8';

        return $htmlPart;
    }

    protected function createPlainPart(ViewModel $viewModel)
    {
        $text = $this->renderViewModel($viewModel);
        $plainPart = new MimePart($text);
        $plainPart->type = 'text/plain';
        $plainPart->charset = 'utf-8';

        return $plainPart;
    }

    protected function sendMail(Message $message)
    {
        $this->getMailTransport()->send($message);
    }
}
