<?php

namespace EcampCore\Job\Mail;

use EcampCore\Entity\User;
use EcampCore\Job\SendMailJob;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\View\Model\ViewModel;

class MailVerificationSenderJob extends SendMailJob
{
    /**
     * @return \EcampCore\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\User');
    }

    public static function Create(User $user)
    {
        $job = new self();
        $job->userId = $user->getId();
        $job->code = $user->createNewActivationCode();
        $job->enqueue();

        return $job;
    }

    public function perform()
    {
        /* @var \EcampCore\Entity\User $user */
        $user = $this->getUserRepository()->find($this->userId);
        $code = $this->code;

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-core/mail/mail-verification-html.twig');
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('code', $code);

        $mimeMessage = new MimeMessage();
        $mimeMessage->addPart($this->createHtmlPart($viewModel));

        $mail = new Message();
        $mail->setTo($user->getUntrustedEmail());
        //$mail->setFrom('no-reply@ecamp3.ch');
        $mail->setBody($mimeMessage);

        $this->sendMail($mail);

    }
}
