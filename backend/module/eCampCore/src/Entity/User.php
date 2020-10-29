<?php

namespace eCamp\Core\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * @ORM\Entity(repositoryClass="eCamp\Core\Repository\UserRepository")
 */
class User extends AbstractCampOwner implements RoleInterface {
    const STATE_NONREGISTERED = 'non-registered';
    const STATE_REGISTERED = 'registered';
    const STATE_ACTIVATED = 'activated';
    const STATE_DELETED = 'deleted';

    const ROLE_GUEST = 'guest';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    const RELATION_ME = 'me';
    const RELATION_KNOWN = 'known';
    const RELATION_UNRELATED = 'unrelated';

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="UserIdentity", mappedBy="user", orphanRemoval=true)
     */
    protected $userIdentities;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="GroupMembership", mappedBy="user", orphanRemoval=true)
     */
    protected $memberships;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="user", orphanRemoval=true)
     */
    protected $collaborations;

    /**
     * Unique username, lower alphanumeric symbols and underscores only.
     *
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true, unique=true)
     */
    private $username;

    /**
     * Users firstname.
     *
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $firstname;

    /**
     * Users surname.
     *
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $surname;

    /**
     * Users nickname.
     *
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $nickname;

    /**
     * @var MailAddress
     * @ORM\OneToOne(targetEntity="MailAddress", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn
     */
    private $trustedMailAddress;

    /**
     * @var MailAddress
     * @ORM\OneToOne(targetEntity="MailAddress", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn
     */
    private $untrustedMailAddress;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $state;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $role;

    /**
     * @var Login
     * @ORM\OneToOne(targetEntity="Login", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private $login;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $language;

    /**
     * @var \DateTime
     * @orm\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $lastCampId;

    public function __construct() {
        parent::__construct();

        $this->state = self::STATE_NONREGISTERED;
        $this->role = self::ROLE_GUEST;

        $this->memberships = new ArrayCollection();
        $this->collaborations = new ArrayCollection();
        $this->userIdentities = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getRoleId() {
        return $this->role ?: self::ROLE_GUEST;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname) {
        $this->firstname = $firstname;
    }

    public function getSurname(): ?string {
        return $this->surname;
    }

    public function setSurname(?string $surname) {
        $this->surname = $surname;
    }

    public function getNickname(): ?string {
        return $this->nickname;
    }

    public function setNickname(?string $nickname) {
        $this->nickname = $nickname;
    }

    /**
     * @return string
     */
    public function getDisplayName() {
        if (!empty($this->nickname)) {
            return $this->nickname;
        }
        if (!empty($this->firstname)) {
            if (!empty($this->surname)) {
                return $this->firstname.' '.$this->surname;
            }

            return $this->firstname;
        }

        return $this->username;
    }

    /**
     * @param $userId
     *
     * @return string
     */
    public function getRelation($userId) {
        if ($userId == $this->id) {
            return self::RELATION_ME;
        }

        $known = false;
        if (!$known) {
            $camps = $this->getOwnedCamps();
            $known |= $camps->exists(function ($idx, Camp $c) use ($userId) {
                return $c->isCollaborator($userId);
            });
        }
        if (!$known) {
            $camps = $this->getCampCollaborations()->filter(function (CampCollaboration $cc) {
                return $cc->isEstablished();
            })->map(function (CampCollaboration $cc) {
                return $cc->getCamp();
            });
            $known |= $camps->exists(function ($idx, Camp $c) use ($userId) {
                return $c->isCollaborator($userId);
            });
        }
        if ($known) {
            return self::RELATION_KNOWN;
        }

        return self::RELATION_UNRELATED;
    }

    public function getTrustedMailAddress(): string {
        if (null === $this->trustedMailAddress) {
            return '';
        }

        return $this->trustedMailAddress->getMail();
    }

    public function setTrustedMailAddress(string $mail) {
        if (null == $this->trustedMailAddress) {
            $this->trustedMailAddress = new MailAddress();
        }
        $this->untrustedMailAddress = null;
        $this->trustedMailAddress->setMail($mail);
    }

    public function getUntrustedMailAddress(): string {
        if (null === $this->untrustedMailAddress) {
            return '';
        }

        return $this->untrustedMailAddress->getMail();
    }

    public function setMailAddress(string $mailAddress): string {
        if ($this->getTrustedMailAddress() !== $mailAddress) {
            if (null === $this->untrustedMailAddress) {
                $this->untrustedMailAddress = new MailAddress();
            }
            $this->untrustedMailAddress->setMail($mailAddress);

            return $this->untrustedMailAddress->createVerificationCode();
        }
        $this->untrustedMailAddress = null;

        return '';
    }

    /**
     * @throws \Exception
     */
    public function verifyMailAddress(string $hash): bool {
        if (self::STATE_NONREGISTERED === $this->state) {
            throw new \Exception('Verification failed.');
        }
        if (self::STATE_DELETED === $this->state) {
            throw new \Exception('Verification failed.');
        }

        if (null === $this->untrustedMailAddress) {
            throw new \Exception('Verification failed.');
        }

        $verified = $this->untrustedMailAddress->verify($hash);

        if ($verified) {
            if (self::STATE_REGISTERED === $this->state) {
                $this->state = self::STATE_ACTIVATED;
            }

            $this->trustedMailAddress = $this->untrustedMailAddress;
            $this->untrustedMailAddress = null;
        }

        return $verified;
    }

    public function getState(): string {
        return $this->state;
    }

    public function setState(string $state): void {
        $this->state = $state;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): void {
        $this->role = $role;
    }

    /**
     * @return Login
     */
    public function getLogin() {
        return $this->login;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function getBirthday() {
        return (null !== $this->birthday) ? (clone $this->birthday) : null;
    }

    public function setBirthday(?DateTime $birthday) {
        if (null !== $birthday) {
            $birthday = clone $birthday;
            $birthday->setTime(0, 0, 0);
        }
        $this->birthday = $birthday;
    }

    public function getLastCampId() {
        return $this->lastCampId;
    }

    public function setLastCampId($lastCampId) {
        $this->lastCampId = $lastCampId;
    }

    public function getGroupMemberships(): ArrayCollection {
        return $this->memberships;
    }

    public function addGroupMembership(GroupMembership $membership) {
        $membership->setUser($this);
        $this->memberships->add($membership);
    }

    public function removeGroupMembership(GroupMembership $membership) {
        $membership->setUser(null);
        $this->memberships->removeElement($membership);
    }

    public function getCampCollaborations() {
        return $this->collaborations;
    }

    public function addCampCollaboration(CampCollaboration $collaboration) {
        $collaboration->setUser($this);
        $this->collaborations->add($collaboration);
    }

    public function removeCampCollaboration(CampCollaboration $collaboration) {
        $collaboration->setUser(null);
        $this->collaborations->removeElement($collaboration);
    }

    public function getUserIdentities(): ArrayCollection {
        return $this->userIdentities;
    }

    public function addUserIdentity(UserIdentity $userIdentity) {
        $userIdentity->setUser($this);
        $this->userIdentities->add($userIdentity);
    }

    public function removeUserIdentity(UserIdentity $userIdentity) {
        $userIdentity->setUser(null);
        $this->userIdentities->removeElement($userIdentity);
    }
}
