<?php

namespace EcampCore\Job\Mail;

use EcampCore\Entity\User;
use EcampLib\Job\AbstractSendMailJob;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\View\Model\ViewModel;

class SendEmailVerificationEmailJob extends AbstractSendMailJob
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

        if ($user != null) {
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

        if($user->getState() == User::STATE_REGISTERED){
            $viewModel->setTemplate('ecamp-core/mail/email-verification-registered.twig');
        } else {
            $viewModel->setTemplate('ecamp-core/mail/email-verification-updated.twig');
        }
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('code', $code);

        $mimeMessage = new MimeMessage();
        $mimeMessage->addPart($this->createHtmlPartByViewModel($viewModel));

        $mail = new Message();
        $mail->setTo($user->getUntrustedEmail());
        //$mail->setFrom('no-reply@ecamp3.ch');
        $mail->setBody($mimeMessage);

        $this->sendMail($mail);
    }
}