<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * MailAddress
 * @ORM\Entity
 * @ORM\Table(name="mail_address")
 */
class MailAddress extends BaseEntity
{
    const STATE_TRUSTED = 'trusted';
    const STATE_UNTRUSTED = 'untrusted';


    public function __construct() {
        parent::__construct();

        $this->state = self::STATE_UNTRUSTED;
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $state;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false, unique=true)
     */
    private $mail;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $verificationCode;



    /**
     * @return mixed
     */
    public function getState() {
        return $this->state;
    }


    /**
     * @return mixed
     */
    public function getMail() {
        return $this->mail;
    }

    public function setMail($mail): void {
        $this->mail = $mail;
    }


    /**
     * @return string
     */
    public function createVerificationCode(): string {
        $hash = hash('sha256', mt_rand());
        $this->verificationCode = md5($hash);

        return $hash;
    }

    /**
     * @param $hash
     * @return bool
     * @throws \Exception
     */
    public function verify($hash): bool {
        if ($this->state === self::STATE_TRUSTED) {
            throw new \Exception("MailAddress already trusted");
        }

        if (md5($hash) === $this->verificationCode) {
            $this->verificationCode = null;
            $this->state = self::STATE_TRUSTED;
        }

        return ($this->state === self::STATE_TRUSTED);
    }

}
