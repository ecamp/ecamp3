<?php

namespace App\Service;

use App\Entity\Camp;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService {
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function sendInviteToCampMail(User $byUser, Camp $camp, string $key, string $emailToInvite): void {
        $frontendUrl = 'http://localhost:3000';
        $email = (new TemplatedEmail())
            ->from('info@ecamp3.ch')
            ->to(new Address($emailToInvite))
            ->subject("You were invited to collaborate in camp {$camp->name}")
            ->htmlTemplate('emails/campCollaborationInvite.html.twig')
            ->textTemplate('emails/campCollaborationInvite.text.twig')
            ->context([
                'by_user' => $byUser->getDisplayName(),
                'url' => "{$frontendUrl}/camps/invitation/{$key}",
                'camp_name' => $camp->name,
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException($e);
        }
    }

    public function sendUserActivationMail(User $user, string $key): void {
        $frontendUrl = 'http://localhost:3000';
        $email = (new TemplatedEmail())
            ->from('info@ecamp3.ch')
            ->to(new Address($user->email))
            ->subject('Welcome to eCamp3')
            ->htmlTemplate('emails/userActivation.html.twig')
            ->textTemplate('emails/userActivation.text.twig')
            ->context([
                'name' => $user->getDisplayName(),
                'url' => "{$frontendUrl}/activate/{$user->getId()}/{$key}",
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException($e);
        }
    }
}
