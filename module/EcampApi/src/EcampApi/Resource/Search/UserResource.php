<?php
namespace EcampApi\Resource\Search;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\Camp;
use EcampApi\Resource\Collaboration\CollaborationBriefResource;
use EcampApi\Resource\Membership\MembershipBriefResource;

class UserResource extends HalResource
{
    public function __construct(
        User $user,
        Group $showMembershipOfGroup = null,
        Camp $showCollaborationOfCamp = null
    ) {
        $object = array(
                'id' => $user->getId(),
                'username' 		=> 	$user->getUsername(),
                'fullname'		=>	$user->getFullName(),
                'displayName' 	=>  $user->getDisplayName(),
                'email'			=> 	$user->getEmail(),
                'tokens' 		=> array($user->getUsername(), $user->getScoutname(), $user->getFirstname(), $user->getSurname()),
                'value'			=>	$user->getId(),
//        		'membership'	=> null,
//        		'collaboration' => null
        );

        if ($showMembershipOfGroup != null) {
            $membership = $user->groupMembership()->getMembership($showMembershipOfGroup);

            if ($membership != null) {
                $object['membership'] = new MembershipBriefResource($membership);
            }
        }

        if ($showCollaborationOfCamp != null) {
            $collaboration = $user->campCollaboration()->getCollaboration($showCollaborationOfCamp);
            if ($collaboration) {
                $object['collaboration'] = new CollaborationBriefResource($collaboration);
            }
        }

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/users', array('user' => $user->getId()));

        $webLink = new Link('web');
        $webLink->setRoute('web', array());

        $this->getLinks()
            ->add($selfLink)
            ->add($webLink);
    }
}
