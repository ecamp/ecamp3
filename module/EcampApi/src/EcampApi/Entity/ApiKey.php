<?php

namespace EcampApi\Entity;

use EcampLib\Entity\BaseEntity;
use EcampCore\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="api_keys", uniqueConstraints={@ORM\UniqueConstraint(name="user_app_device", columns={"user_id", "app_name", "device_name"})}))
 */
class ApiKey extends BaseEntity
{

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="EcampCore\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(name="api_key", type="string", nullable=false)
     */
    private $apiKey;

    /**
     * @ORM\Column(name="app_name", type="string", nullable=false)
     */
    private $appName;

    /**
     * @ORM\Column(name="device_name", type="string", nullable=true)
     */
    private $deviceName;

    public function __construct(User $user, $appName, $deviceName = null)
    {
        parent::__construct();

        $this->user = $user;
        $this->apiKey = md5($this->id) . md5(uniqid());
        $this->appName = $appName;
        $this->deviceName = $deviceName;
    }

    /**
     * @return \EcampCore\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @return string
     */
    public function getDeviceName()
    {
        return $this->deviceName;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return boolean
     */
    public function checkApiKey($apiKey)
    {
        return $apiKey == $this->apiKey;
    }
}
