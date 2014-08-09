<?php

namespace EcampCore\Job;

use EcampCore\Entity\User;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\View\Model\ViewModel;

class SendActivationMailJob extends SendMailJob
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
        $viewModel->setTemplate('ecamp-core/mail/activation-html.twig');
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('code', $code);

        $mimeMessage = new MimeMessage();
        $mimeMessage->addPart($this->createHtmlPart($viewModel));

        $mail = new Message();
        $mail->setTo($user->getEmail());
        //$mail->setFrom('no-reply@ecamp3.ch');
        $mail->setBody($mimeMessage);

        $this->sendMail($mail);
    }
}
