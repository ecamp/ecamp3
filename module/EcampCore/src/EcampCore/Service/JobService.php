<?php

namespace EcampCore\Service;

use EcampCore\Entity\Job;
use EcampCore\Entity\Day;
use EcampCore\Repository\JobRespRepository;
use EcampLib\Service\ServiceBase;
use EcampCore\Entity\JobResp;

class JobService
    extends ServiceBase
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
