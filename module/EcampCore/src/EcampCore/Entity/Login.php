<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;

use EcampLib\Entity\BaseEntity;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\LoginRepository")
 * @ORM\Table(name="logins")
 */
class Login
    extends BaseEntity
{

    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
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
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $pwResetKey;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", mappedBy="login")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Returns the User of this Login Entity
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Create a new PW Reset Key
     */
    public function createPwResetKey()
    {
        $this->pwResetKey = md5(microtime(true));

        return $this->pwResetKey;
    }

    /**
     * Clears the pwResetKey Field.
     */
    public function clearPwResetKey()
    {
        $this->pwResetKey = null;
    }

    /**
     * Returns the PwResetKey
     * @return string
     */
    public function getPwResetKey()
    {
        return $this->pwResetKey;
    }

    /**
     * Sets a new Password. It creates a new salt
     * ans stores the salten password
     * @param string $password
     */
    public function setNewPassword($password)
    {
        $this->salt = md5(microtime(true));
        $this->password = $this->getHash($password);
    }

    /**
     * Checks the given Password
     * Returns true, if the given password matches for this Login
     *
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword($password)
    {
        return ($this->getHash($password) == $this->password);
    }

    private function getHash($password)
    {
        $options = array(
            'cost' => 10,
            'salt' => $this->salt
        );

        return \password_hash($password, PASSWORD_BCRYPT, $options);
    }
}
