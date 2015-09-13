<?php

namespace EcampLib\Job;

use Zend\Mail\Message;
use Zend\Mail\Transport\Factory;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\View\Model\ViewModel;

abstract class AbstractSendMailJob extends AbstractJobBase
{
    /**
     * @return \Zend\View\View
     */
    private function getView()
    {
        return $this->getService('View');
    }

    /**
     * @return \Zend\Mail\Transport\TransportInterface
     */
    private function getMailTransport()
    {
        $config = $this->getService('Config');
        $transportConfig = $config['mail']['transport'] ?: array();

        return Factory::create($transportConfig);
    }

    protected function createHtmlPartByViewModel(ViewModel $viewModel)
    {
        return $this->createPartByViewModel($viewModel, 'text/html');
    }

    protected function createTextPartByViewModel(ViewModel $viewModel)
    {
        return $this->createPartByViewModel($viewModel, 'text/plain');
    }

    protected function createPartByViewModel(ViewModel $viewModel, $type)
    {
        $content = $this->render($viewModel);
        $part = new MimePart($content);
        $part->encoding = Mime::ENCODING_8BIT;
        $part->type = $type;
        $part->charset = 'utf-8';

        return $part;
    }

    /**
     * @param  ViewModel $viewModel
     * @return string
     */
    protected function render(ViewModel $viewModel)
    {
        $viewModel->setOption('has_parent', true);

        /** @noinspection PhpVoidFunctionResultUsedInspection */

        return $this->getView()->render($viewModel);
    }

    protected function sendMail(Message $message)
    {
        $this->getMailTransport()->send($message);
    }
}
