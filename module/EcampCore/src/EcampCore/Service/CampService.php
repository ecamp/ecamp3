<?php

namespace EcampCore\Service;

use EcampCore\Acl\Privilege;
use EcampCore\Entity\Camp;
use EcampCore\Entity\CampCollaboration;
use EcampCore\Repository\CampRepository;

use EcampLib\Service\ServiceBase;
use EcampLib\Validation\ValidationException;

class CampService
    extends ServiceBase
{
    /** @var CampRepository */
    private $campRepo;

    /** @var PeriodService */
    private $periodService;

    public function __construct(
        CampRepository $campRepo,
        PeriodService $periodService
    ){
        $this->campRepo = $campRepo;
        $this->periodService = $periodService;
    }

    /**
     * Returns the requested Camp
     *
     * For an integer the Camp with the given id
     * For NULL the Camp from the Context
     *
     * @param $id
     * @return \EcampCore\Entity\Camp | NULL
     */
    public function Get($id)
    {
        $camp = null;

        if (is_string($id)) {
            $camp = $this->campRepo->find($id);
        }

        if ($id instanceof Camp) {
            $camp = $id;
        }

        if ($camp != null) {
            return $this->aclIsAllowed($camp, Privilege::CAMP_LIST) ? $camp : null;
        }

        return null;
    }

    /**
     * Deletes the current Camp
     */
    public function Delete(Camp $camp)
    {
        $this->aclRequire($camp, Privilege::CAMP_ADMINISTRATE);

        $this->remove($camp);
    }

    /**
     * Updates the current Camp
     *
     * @return \EcampCore\Entity\Camp
     */
    public function Update($campId, $data)
    {
        $camp = $this->Get($campId);
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $campValidationForm = $this->createValidationForm(
            $camp, $data, array_intersect(array_keys($data), array('title', 'motto')));

        if (!$campValidationForm->isValid()) {
            throw ValidationException::FromForm($campValidationForm);
        }

        return $camp;
    }

    /**
     * Creats a new Camp
     * If Group is defined, the Camp belongs to this Group.
     * Otherwise it belongs to the User.
     *
     */
    public function Create($data)
    {
        $camp = new Camp();
        $campCollaboration = new CampCollaboration($this->getMe(), $camp, null,
            CampCollaboration::STATUS_ESTABLISHED, CampCollaboration::ROLE_MANAGER);

        $campValidationForm = $this->createValidationForm(
            $camp, $data, array('name', 'title', 'motto', 'campType', 'owner'));

        if (!$campValidationForm->isValid()) {
            throw ValidationException::FromForm($campValidationForm);
        }

        $camp->setCreator($this->getMe());

        try {
            $periodData = $data['period'];
            $periodData['description'] = $data['name'];

            $this->periodService->Create($camp, $periodData);
        } catch (ValidationException $ex) {
            throw ValidationException::FromInnerException('period', $ex);
        }

        $this->persist($camp);
        $this->persist($campCollaboration);

        return $camp;
    }

}
