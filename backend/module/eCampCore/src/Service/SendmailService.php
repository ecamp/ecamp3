<?php

namespace eCamp\Core\Service;

use eCamp\Core\Entity\User;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;
use Laminas\View\Model\ViewModel;
use Laminas\View\View;
use ZendTwig\View\TwigModel;

class SendmailService extends AbstractService {
    /** @var TransportInterface */
    private $mailTransport;

    /** @var View */
    private $view;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        TransportInterface $mailTransport,
        View $view
    ) {
        parent::__construct($serviceUtils, $authenticationService);

        $this->mailTransport = $mailTransport;
        $this->view = $view;
    }

    public function sendRegisterMail(User $user, $key) {
        $url = '';

        $textViewModel = $this->createViewModel('mail/register-text');
        $textViewModel->setVariable('user', $user);
        $textViewModel->setVariable('url', $url);
        $textPart = $this->createPart($textViewModel, Mime::TYPE_TEXT);

        $htmlViewModel = $this->createViewModel('mail/register-html');
        $htmlViewModel->setVariable('user', $user);
        $htmlViewModel->setVariable('url', $url);
        $htmlPart = $this->createPart($htmlViewModel, Mime::TYPE_HTML);

        $mail = $this->createMail($textPart, $htmlPart);
        //$mail->setFrom('no-reply@ecamp3.ch');
        $mail->setTo($user->getUntrustedMailAddress());

        $this->mailTransport->send($mail);
    }

    protected function createMail(...$parts) {
        $bodyPart = $this->createMultipart($parts);
        $mimeMessage = new MimeMessage();
        $mimeMessage->addPart($bodyPart);
        $mail = new Message();
        $mail->setBody($mimeMessage);

        return $mail;
    }

    protected function createMultipart(array $parts) {
        $partsMessage = new MimeMessage();
        $partsMessage->setParts($parts);
        $contentPart = new MimePart($partsMessage->generateMessage());
        $contentPart->type = Mime::MULTIPART_ALTERNATIVE;
        $contentPart->boundary = $partsMessage->getMime()->boundary();

        return $contentPart;
    }

    protected function createPart(ViewModel $viewModel, $type) {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $content = $this->view->render($viewModel);
        $part = new MimePart($content);
        $part->encoding = Mime::ENCODING_8BIT;
        $part->type = $type;
        $part->charset = 'utf-8';

        return $part;
    }

    protected function createViewModel($template) {
        $viewModel = new TwigModel();
        $viewModel->setOption('has_parent', true);
        $viewModel->setTemplate($template);

        return $viewModel;
    }
}
