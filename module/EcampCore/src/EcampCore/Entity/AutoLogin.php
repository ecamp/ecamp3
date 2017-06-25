<?php

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;
use EcampLib\Entity\BaseEntity;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\AutoLoginRepository")
 * @ORM\Table(name="autologins")
 */
class AutoLogin
    extends BaseEntity
{

    const COOKIE_NAME = 'autologin-token';
    const COOKIE_EXPIRES = 8640000; // 100 Tage

    public function __construct(User $user)
    {
        parent::__construct();

        $this->user = $user;
    }

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $autologinToken;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function createToken()
    {
        $token = $this->getRandomString();
        $this->autologinToken = self::GetHash($token);

        return $token;
    }

    public static function GetHash($token)
    {
        $options = array(
            'cost' => 10,
            'salt' => "static-salt1234567890!"
        );

        return \password_hash($token, PASSWORD_BCRYPT, $options);
    }

    private function getRandomString()
    {
        return md5(microtime(true));
    }
}
