<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * MailAddress.
 *
 * @ORM\Entity
 */
class MailAddress extends BaseEntity {
    public const STATE_TRUSTED = 'trusted';
    public const STATE_UNTRUSTED = 'untrusted';

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private string $state;

    /**
     * @ORM\Column(type="string", length=64, nullable=false, unique=true)
     */
    private ?string $mail = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $verificationCode = null;

    public function __construct() {
        parent::__construct();

        $this->state = self::STATE_UNTRUSTED;
    }

    public function getState(): string {
        return $this->state;
    }

    public function getMail(): ?string {
        return $this->mail;
    }

    public function setMail(?string $mail): void {
        $this->mail = $mail;
    }

    public function createVerificationCode(): string {
        $hash = hash('sha256', mt_rand());
        $this->verificationCode = md5($hash);

        return $hash;
    }

    /**
     * @throws \Exception
     */
    public function verify(string $hash): bool {
        if (self::STATE_TRUSTED === $this->state) {
            throw new \Exception('MailAddress already trusted');
        }

        if (md5($hash) === $this->verificationCode) {
            $this->verificationCode = null;
            $this->state = self::STATE_TRUSTED;
        }

        return self::STATE_TRUSTED === $this->state;
    }
}
