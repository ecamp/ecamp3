<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class Login extends BaseEntity {
    const CURRENT_HASH_VERSION = 1;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $password;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private ?string $salt;

    /**
     * @ORM\Column(type="string", length=64, nullable=true, unique=true)
     */
    private ?string $pwResetKey;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="login")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $hashVersion = self::CURRENT_HASH_VERSION;

    public function __construct(User $user, string $password) {
        parent::__construct();

        $this->user = $user;
        $this->setNewPassword($password);
    }

    /**
     * Returns the User of this Login Entity.
     */
    public function getUser(): User {
        return $this->user;
    }

    public function getHashVersion(): int {
        return $this->hashVersion;
    }

    /**
     * Create a new PW Reset Key.
     */
    public function createPwResetKey(): string {
        $pwResetKey = md5(microtime(true));
        $this->pwResetKey = $this->getHash($pwResetKey);

        return $pwResetKey;
    }

    /**
     * Clears the pwResetKey Field.
     */
    public function clearPwResetKey(): void {
        $this->pwResetKey = null;
    }

    /**
     * @param $pwResetKey
     */
    public function checkPwResetKey($pwResetKey): bool {
        return (null != $this->pwResetKey) && $this->checkHash($pwResetKey, $this->pwResetKey);
    }

    /**
     * @param $pwResetKey
     * @param $password
     * @param null $hashVersion
     *
     * @throws \Exception
     */
    public function resetPassword(string $pwResetKey, string $password, ?int $hashVersion = null): void {
        if ($this->checkPwResetKey($pwResetKey)) {
            $this->setNewPassword($password, $hashVersion);
        } else {
            throw new \Exception('Invalid reset-key.');
        }
    }

    /**
     * @param $oldPassword
     * @param $newPassword
     * @param null $hashVersion
     *
     * @throws \Exception
     */
    public function changePassword(string $oldPassword, string $newPassword, ?int $hashVersion = null): void {
        if ($this->checkPassword($oldPassword)) {
            $this->setNewPassword($newPassword, $hashVersion);
        } else {
            throw new \Exception('Password incorrect.');
        }
    }

    /**
     * Checks the given Password
     * Returns true, if the given password matches for this Login.
     */
    public function checkPassword(string $password, bool $rehash = false): bool {
        if ($this->checkHash($password, $this->password)) {
            if ($rehash) {
                $this->setNewPassword($password);
            }

            return true;
        }

        return false;
    }

    /**
     * Sets a new Password. It creates a new salt and stores the salten password.
     */
    private function setNewPassword(string $password, ?int $hashVersion = null): void {
        $this->createSalt();
        $this->hashVersion = $hashVersion ?? self::CURRENT_HASH_VERSION;
        $this->password = $this->getHash($password);
    }

    private function createSalt(): void {
        $this->password = null;
        $this->pwResetKey = null;
        $this->salt = md5(microtime(true));
    }

    private function getHash(string $password): string {
        switch ($this->hashVersion) {
            case 1:
                return $this->getHash_v1($password);

            default:
                return $password;
        }
    }

    private function checkHash(string $password, string $hash): bool {
        switch ($this->hashVersion) {
            case 1:
                return $this->checkHash_v1($password, $hash);

            default:
                return $password == $hash;
        }
    }

    // HASH - VERSION 1

    private function getHash_v1(string $password): string {
        return password_hash($this->addSalt_v1($password), PASSWORD_BCRYPT, ['cost' => 10]);
    }

    private function checkHash_v1(string $password, string $hash): bool {
        return password_verify($this->addSalt_v1($password), $hash);
    }

    private function addSalt_v1(string $password): string {
        return $this->salt.$password;
    }
}
