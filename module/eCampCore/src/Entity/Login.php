<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="logins")
 */
class Login extends BaseEntity {
    const CURRENT_HASH_VERSION = 0;

    public function __construct(User $user, $password) {
        parent::__construct();
        $this->user = $user;
        $this->setNewPassword($password);
    }

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $salt;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true, unique=true)
     */
    private $pwResetKey;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", mappedBy="login")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $hashVersion = null;


    /**
     * Returns the User of this Login Entity
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getHashVersion() {
        return $this->hashVersion;
    }

    /**
     * Create a new PW Reset Key
     */
    public function createPwResetKey() {
        $pwResetKey = md5(microtime(true));
        $this->pwResetKey = $this->getHash($pwResetKey);
        return $pwResetKey;
    }

    /**
     * Clears the pwResetKey Field.
     */
    public function clearPwResetKey() {
        $this->pwResetKey = null;
    }

    /**
     * @param $pwResetKey
     * @return bool
     */
    public function checkPwResetKey($pwResetKey) {
        return $this->checkHash($pwResetKey, $this->pwResetKey);
    }

    /**
     * @param $pwResetKey
     * @param $password
     * @param null $hashVersion
     * @throws \Exception
     */
    public function resetPassword($pwResetKey, $password, $hashVersion = null) {
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
     * @throws \Exception
     */
    public function changePassword($oldPassword, $newPassword, $hashVersion = null) {
        if ($this->checkPassword($oldPassword)) {
            $this->setNewPassword($newPassword, $hashVersion);
        } else {
            throw new \Exception('Password incorrect.');
        }
    }

    /**
     * Checks the given Password
     * Returns true, if the given password matches for this Login
     *
     * @param string $password
     *
     * @param bool $rehash
     * @return bool
     */
    public function checkPassword($password, $rehash = false) {
        if ($this->checkHash($password, $this->password)) {
            if ($rehash) {
                $this->setNewPassword($password);
            }
            return true;
        }
        return false;
    }

    /**
     * Sets a new Password. It creates a new salt and stores the salten password
     * @param string $password
     * @param null $hashVersion
     */
    private function setNewPassword($password, $hashVersion = null) {
        $this->createSalt();
        $this->hashVersion = ($hashVersion !== null) ? $hashVersion : self::CURRENT_HASH_VERSION;
        $this->password = $this->getHash($password);
    }

    private function createSalt() {
        $this->password = null;
        $this->pwResetKey = null;
        $this->salt = md5(microtime(true));
    }

    /**
     * @param $password
     * @return string
     */
    private function getHash($password) {
        switch ($this->hashVersion) {
            case 1:
                return $this->getHash_v1($password);
            default:
                return $password;
        }
    }

    /**
     * @param $password
     * @param $hash
     * @return bool
     */
    private function checkHash($password, $hash) {
        switch ($this->hashVersion) {
            case 1:
                return $this->checkHash_v1($password, $hash);
            default:
                return ($password == $hash);
        }
    }

    /****************************  HASH - VERSION 1  ****************************/
    /**
     * @param $password
     * @return string
     */
    private function getHash_v1($password) {
        return password_hash($this->addSalt_v1($password), PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * @param $password
     * @param $hash
     * @return bool
     */
    private function checkHash_v1($password, $hash) {
        return password_verify($this->addSalt_v1($password), $hash);
    }

    /**
     * @param $password
     * @return string
     */
    private function addSalt_v1($password) {
        return $this->salt . $password;
    }
    /****************************************************************************/
}
