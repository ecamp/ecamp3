<?php

namespace EcampCore\Acl;

use EcampLib\Acl\Acl;
use EcampLib\Acl\ResourceFactoryInterface;
use EcampCore\Acl\BelongsToParentResource;
use EcampCore\Acl\Resource\UserResource;
use EcampCore\Acl\Resource\GroupResource;
use EcampCore\Acl\Resource\CampResource;

use EcampLib\Entity\BaseEntity;
use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\Camp;

class EntityResourceFactory
    implements ResourceFactoryInterface
{
    /**
     * @var Acl
     */
    private $acl;

    public function __construct(Acl $acl)
    {
        $this->acl = $acl;
    }

    public function createResource($entity)
    {
        if (! $entity instanceof BaseEntity) {
            throw new \InvalidArgumentException("Instance of EcampLib\Entity\BaseEntity expected!");
        }

        if ($entity instanceof BelongsToParentResource) {
            return $this->createResource($entity->getParentResource());
        }

        if ($entity instanceof User) {
            return $this->createUserResource($entity);
        }

        if ($entity instanceof Group) {
            return $this->createGroupResource($entity);
        }

        if ($entity instanceof Camp) {
            return $this->createCampResource($entity);
        }

    }

    private function createUserResource(User $user)
    {
        $resource = new UserResource($user);

        if (! $this->acl->hasResource($resource)) {
            $this->acl->addResource($resource, 'EcampCore\Entity\User');
        }

        return $resource;
    }

    private function createGroupResource(Group $group)
    {
        $resource = new GroupResource($group);

        if (! $this->acl->hasResource($resource)) {
            $this->acl->addResource($resource, 'EcampCore\Entity\Group');
        }

        return $resource;
    }

    private function createCampResource(Camp $camp)
    {
        $resource = new CampResource($camp);

        if (! $this->acl->hasResource($resource)) {
            $this->acl->addResource($resource, 'EcampCore\Entity\Camp');
        }

        return $resource;
    }

}
