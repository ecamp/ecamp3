<?php

namespace EcampCore\Job\Mail;

use EcampCore\Entity\Login;
use EcampCore\Repository\LoginRepository;
use EcampLib\Job\AbstractSendMailJob;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mvc\ApplicationInterface;
use Zend\View\Model\ViewModel;

/**
 * Class SendPwResetMailJob
 * @property string loginId
 * @property string resetKey
 *
 * @package EcampCore\Job\Login
 */
class SendPwResetMailJob extends AbstractSendMailJob
{
    /** @var LoginRepository */
    private $loginRepository;

    /**
     * SendEmailVerificationEmailJob constructor.
     * @param Login $login
     */
    public function __construct(Login $login = null)
    {
        if ($login != null) {
            $this->loginId = $login->getId();
            $this->resetKey = $login->createPwResetKey();
        }
    }


    public function doInit(ApplicationInterface $app)
    {
        parent::doInit($app);

        $this->loginRepository = $app->getServiceManager()->get('EcampCore\Repository\Login');
    }

    public function doExecute(ApplicationInterface $app)
    {
        /* @var \EcampCore\Entity\Login $login */
        $login = $this->loginRepository->find($this->loginId);
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

