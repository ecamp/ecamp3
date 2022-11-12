<?php

namespace App\Service;

use App\DTO\ResetPassword;
use App\Entity\CampCollaboration;
use App\Entity\Profile;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class MailService {
    public const TRANSLATE_DOMAIN = 'email';

    public function __construct(
        private MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private Security $security,
        private Environment $twigEnironment,
        private string $frontendBaseUrl,
        private string $senderEmail,
        private string $senderName = ''
    ) {
    }

    public function sendInviteToCampMail(CampCollaboration $campCollaboration): void {
        if (CampCollaboration::STATUS_INVITED == $campCollaboration->status && $campCollaboration->getEmail()) {
            /** @var User $byUser */
            $byUser = $this->security->getUser();

            $camp = $campCollaboration->getCamp();
            $key = $campCollaboration->inviteKey;
            $emailToInvite = $campCollaboration->getEmail();

            $email = (new TemplatedEmail())
                ->from(new Address($this->senderEmail, $this->senderName))
                ->to(new Address($emailToInvite))
                ->subject($this->translator->trans('inviteToCamp.subject', ['campName' => $camp->name], self::TRANSLATE_DOMAIN, $byUser->profile->language))
                ->htmlTemplate($this->getTemplate('emails/campCollaborationInvite.{language}.html.twig', $byUser))
                ->textTemplate($this->getTemplate('emails/campCollaborationInvite.{language}.text.twig', $byUser))
                ->context([
                    'by_user' => $byUser->getDisplayName(),
                    'url' => "{$this->frontendBaseUrl}/camps/invitation/{$key}",
                    'camp_name' => $camp->name,
                    'camp_title' => $camp->title,
                ])
            ;

            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                throw new \RuntimeException($e);
            }
        }
    }

    public function sendUserActivationMail(User $user, string $key): void {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to(new Address($user->getEmail()))
            ->subject($this->translator->trans('userActivation.subject', [], self::TRANSLATE_DOMAIN, $user->profile->language))
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

    public function sendPasswordResetLink(User $user, ResetPassword $data): void {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to(new Address($user->getEmail()))
            ->subject($this->translator->trans('passwordReset.subject', [], self::TRANSLATE_DOMAIN, $user->profile->language))
            ->htmlTemplate($this->getTemplate('emails/passwordResetLink.{language}.html.twig', $user))
            ->textTemplate($this->getTemplate('emails/passwordResetLink.{language}.text.twig', $user))
            ->context([
                'name' => $user->getDisplayName(),
                'url' => "{$this->frontendBaseUrl}/reset-password/{$data->id}",
            ])
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException($e);
        }
    }

    public function sendEmailVerificationMail(User $user, Profile $data): void {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to(new Address($data->untrustedEmail))
            ->subject($this->translator->trans('emailVerification.subject', [], self::TRANSLATE_DOMAIN, $user->profile->language))
            ->htmlTemplate($this->getTemplate('emails/verifyMailAdress.{language}.html.twig', $user))
            ->textTemplate($this->getTemplate('emails/verifyMailAdress.{language}.text.twig', $user))
            ->context([
                'name' => $user->getDisplayName(),
                'oldMail' => $data->email,
                'newMail' => $data->untrustedEmail,
                'url' => "{$this->frontendBaseUrl}/profile/verify-mail/{$data->untrustedEmailKey}",
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

            if ($this->twigEnironment->getLoader()->exists($template)) {
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
