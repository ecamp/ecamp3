<?php

namespace EcampCore\Service;

use EcampCore\Acl\Privilege;
use EcampCore\Entity\Camp;
use EcampCore\Entity\Day;
use EcampCore\Entity\Job;
use EcampCore\Entity\JobResp;
use EcampCore\Repository\JobRespRepository;
use EcampLib\Validation\ValidationException;

class JobService extends Base\ServiceBase
{

    /**
     * @var \EcampCore\Repository\JobRespRepository
     */
    private $jobRespRepository;

    public function __construct(
        JobRespRepository $jobRespRepository
    ){
        $this->jobRespRepository = $jobRespRepository;
    }

    public function Create(Camp $camp, $data)
    {
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $job = new Job($camp);

        $validationForm = $this->createValidationForm($job, $data, array('name'));
        if ($validationForm->isValid()) {
            $this->persist($job);
        }

        return $job;
    }

    public function Update(Job $job, $data)
    {
        $camp = $job->getCamp();
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $validationForm = $this->createValidationForm($job, $data, array('name'));

        if (! $validationForm->isValid()) {
            throw ValidationException::FromForm($validationForm);
        }

        return $job;
    }

    public function Delete(Job $job)
    {
        $camp = $job->getCamp();
        $this->aclRequire($camp, Privilege::CAMP_CONFIGURE);

        $this->remove($job);
    }

    public function setResponsableUsers(Job $job, Day $day, array $users)
    {
        $jobResps =
            $this->jobRespRepository->findBy(array(
                'job' => $job,
                'day' => $day
            ));

        foreach ($jobResps as $jobResp) {
            /* @var $jobResp \EcampCore\Entity\JobResp */
            if (!in_array($jobResp->getUser(), $users)) {
                $this->remove($jobResp);
            }
        }

        foreach ($users as $user) {
            if (!$job->isUserResp($day, $user)) {
                $campCollaboration = $day->getCamp()->campCollaboration()->getCollaboration($user);
                $jobResp = new JobResp($day, $job, $campCollaboration);
                $this->persist($jobResp);
            }
        }
    }

}
