<?php

namespace EcampCore\Job;

use EcampCore\Entity\User;
use EcampLib\Job\AbstractSendMailJob;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\View\Model\ViewModel;

class SendActivationMailJob extends AbstractSendMailJob
{
    /**
     * @return \EcampCore\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->getService('EcampCore\Repository\User');
    }

    public function __construct(User $user = null)
    {
        parent::__construct();

        if ($user) {
            $this->userId = $user->getId();
            $this->code = $user->createNewActivationCode();
        }
    }

    public function execute()
    {
        /* @var \EcampCore\Entity\User $user */
        $user = $this->getUserRepository()->find($this->userId);
        $code = $this->code;

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-core/mail/activation-html.twig');
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('code', $code);

        $mimeMessage = new MimeMessage();
        $mimeMessage->addPart($this->createHtmlPartByViewModel($viewModel));

        $mail = new Message();
        $mail->setTo($user->getEmail());
        //$mail->setFrom('no-reply@ecamp3.ch');
        $mail->setBody($mimeMessage);

        $this->sendMail($mail);
    }
}
