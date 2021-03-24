<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Types\DateUtc;
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
     * @ORM\OneToMany(targetEntity="UserIdentity", mappedBy="user", orphanRemoval=true)
     */
    protected Collection $userIdentities;

    /**
     * @ORM\OneToMany(targetEntity="GroupMembership", mappedBy="user", orphanRemoval=true)
     */
    protected Collection $memberships;

    /**
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="user", orphanRemoval=true)
     */
    protected Collection $collaborations;

    /**
     * Unique username, lower alphanumeric symbols and underscores only.
     *
     * @ORM\Column(type="string", length=32, nullable=true, unique=true)
     */
    private ?string $username = null;

    /**
     * Users firstname.
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $firstname = null;

    /**
     * Users surname.
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $surname = null;

    /**
     * Users nickname.
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $nickname = null;

    /**
     * @ORM\OneToOne(targetEntity="MailAddress", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn
     */
    private ?MailAddress $trustedMailAddress = null;

    /**
     * @ORM\OneToOne(targetEntity="MailAddress", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn
     */
    private ?MailAddress $untrustedMailAddress = null;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private string $state;

    /**
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private string $role;

    /**
     * @ORM\OneToOne(targetEntity="Login", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private ?Login $login;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private ?string $language = null;

    /**
     * @orm\Column(type="date", nullable=true)
     */
    private ?DateUtc $birthday = null;

    public function __construct() {
        parent::__construct();

        $this->state = self::STATE_NONREGISTERED;
        $this->role = self::ROLE_GUEST;

        $this->memberships = new ArrayCollection();
        $this->collaborations = new ArrayCollection();
        $this->userIdentities = new ArrayCollection();
    }

    public function getRoleId(): string {
        return $this->role;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(?string $username): void {
        $this->username = $username;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void {
        $this->firstname = $firstname;
    }

    public function getSurname(): ?string {
        return $this->surname;
    }

    public function setSurname(?string $surname): void {
        $this->surname = $surname;
    }

    public function getNickname(): ?string {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void {
        $this->nickname = $nickname;
    }

    public function getDisplayName(): ?string {
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
     */
    public function getRelation($userId): string {
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

    public function setTrustedMailAddress(string $mail): void {
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

    public function getLogin(): ?Login {
        return $this->login;
    }

    public function getLanguage(): ?string {
        return $this->language;
    }

    public function setLanguage(?string $language): void {
        $this->language = $language;
    }

    public function getBirthday(): ?DateUtc {
        return (null !== $this->birthday) ? (clone $this->birthday) : null;
    }

    public function setBirthday(?DateUtc $birthday): void {
        $this->birthday = null !== $birthday ? clone $birthday : $birthday;
    }

    public function getGroupMemberships(): Collection {
        return $this->memberships;
    }

    public function addGroupMembership(GroupMembership $membership): void {
        $membership->setUser($this);
        $this->memberships->add($membership);
    }

    public function removeGroupMembership(GroupMembership $membership): void {
        $membership->setUser(null);
        $this->memberships->removeElement($membership);
    }

    public function getCampCollaborations(): Collection {
        return $this->collaborations;
    }

    public function addCampCollaboration(CampCollaboration $collaboration): void {
        $collaboration->setUser($this);
        $this->collaborations->add($collaboration);
    }

    public function removeCampCollaboration(CampCollaboration $collaboration): void {
        $collaboration->setUser(null);
        $this->collaborations->removeElement($collaboration);
    }

    public function getUserIdentities(): Collection {
        return $this->userIdentities;
    }

    public function addUserIdentity(UserIdentity $userIdentity): void {
        $userIdentity->setUser($this);
        $this->userIdentities->add($userIdentity);
    }

    public function removeUserIdentity(UserIdentity $userIdentity): void {
        $userIdentity->setUser(null);
        $this->userIdentities->removeElement($userIdentity);
    }
}
