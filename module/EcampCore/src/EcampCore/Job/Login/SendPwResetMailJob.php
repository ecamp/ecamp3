<?php

namespace EcampCore\Job\Login;

use EcampCore\Entity\Login;
use EcampCore\Job\SendMailJob;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\View\Model\ViewModel;

class SendPwResetMailJob extends SendMailJob
{
    /**
     * @return \EcampCore\Repository\LoginRepository
     */
    private function getLoginRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Login');
    }

    public static function Create(Login $login)
    {
        $job = new self();
        $job->loginId = $login->getId();
        $job->resetKey = $login->createPwResetKey();
        $job->enqueue();

        return $job;
    }

    public function perform()
    {
        /* @var \EcampCore\Entity\Login $login */
        $login = $this->getLoginRepository()->find($this->loginId);
        $resetKey = $this->resetKey;

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-core/mail/pw-reset-html.twig');
        $viewModel->setVariable('login', $login);
        $viewModel->setVariable('resetKey', $resetKey);

        $mimeMessage = new MimeMessage();
        $mimeMessage->addPart($this->createHtmlPart($viewModel));

        $mail = new Message();
        $mail->setTo($login->getUser()->getEmail());
        //$mail->setFrom('no-reply@ecamp3.ch');
        $mail->setBody($mimeMessage);

        $this->sendMail($mail);
    }

}
