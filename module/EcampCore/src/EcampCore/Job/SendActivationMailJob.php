<?php

namespace EcampCore\Job;

use EcampCore\Entity\User;
use Zend\Mail\Message;
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

    public static function Create(User $user, $code)
    {
        $job = new self();
        $job->userId = $user->getId();
        $job->code = $code;
        $job->enqueue();

        return $job;
    }

    public function createTextPart()
    {
        return false;
    }

    public function createHtmlPart()
    {
        /* @var \EcampCore\Entity\User $user */
        $user = $this->getUserRepository()->find($this->userId);
        $code = $this->code;

        $viewModel = new ViewModel();
        $viewModel->setTemplate('ecamp-core/mail/activation-html.twig');
        $viewModel->setVariable('user', $user);
        $viewModel->setVariable('code', $code);

        return $this->createHtmlPartByViewModel($viewModel);
    }

    public function completeMail(Message $mail)
    {
        /* @var \EcampCore\Entity\User $user */
        $user = $this->getUserRepository()->find($this->userId);

        $mail->addTo($user->getEmail());
        $mail->setSubject('Welcome at eCamp3');
    }
}
