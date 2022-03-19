<?php

namespace App\Service;

use App\Entity\Camp;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService {
    public function __construct(
        private MailerInterface $mailer,
        private string $frontendBaseUrl,
        private string $mailFrom,
    ) {
    }

    public function sendInviteToCampMail(User $byUser, Camp $camp, string $key, string $emailToInvite): void {
        $email = (new TemplatedEmail())
            ->from($this->mailFrom)
            ->to(new Address($emailToInvite))
            ->subject("You were invited to collaborate in camp {$camp->name}")
            ->htmlTemplate($this->getTemplate('emails/campCollaborationInvite.{language}.html.twig', $byUser))
            ->textTemplate($this->getTemplate('emails/campCollaborationInvite.{language}.text.twig', $byUser))
            ->context([
                'by_user' => $byUser->getDisplayName(),
                'url' => "{$this->frontendBaseUrl}/camps/invitation/{$key}",
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
        $email = (new TemplatedEmail())
            ->from($this->mailFrom)
            ->to(new Address($user->getEmail()))
            ->subject('Welcome to eCamp3')
            ->htmlTemplate($this->getTemplate('emails/userActivation.{language}.html.twig', $user))
            ->textTemplate($this->getTemplate('emails/userActivation.{language}.text.twig', $user))
            ->context([
                'name' => $user->getDisplayName(),
                'url' => "{$this->frontendBaseUrl}/activate/{$user->getId()}/{$key}",
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException($e);
        }
    }

    private function getTemplate(string $templateName, User $user) {
        // TODO: Move this into some configuration
        $languageFallback = [
            'de-CH-scout' => 'de',
            'fr-CH-scout' => 'fr',
            'it-CH-scout' => 'it',
            'en-CH-scout' => 'en',
            'de' => 'en',
            'it' => 'en',
            'fr' => 'en',
        ];

        $language = $user->profile->language ?? 'en';

        while (true) {
            $template = str_replace('{language}', $language, $templateName);

            // TODO: Remove path
            if (file_exists(__DIR__.'/../../templates/'.$template)) {
                return $template;
            }

            if (!isset($languageFallback[$language])) {
                throw new \Exception(
                    "Can not find Mail-Template translated '{$templateName}' for ".
                    ($user->profile->language ?? 'en')
                );
            }

            $language = $languageFallback[$language];
        }
    }
}
