<?php

namespace EcampWeb\Controller\Camp;

use Zend\View\Model\ViewModel;
class JobController extends BaseController
{
    /**
     * @return \EcampCore\Repository\DayRepository
     */
    private function getDayRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Day');
    }

    /**
     * @return \EcampCore\Repository\JobRepository
     */
    private function getJobRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Job');
    }

    /**
     * @return \EcampCore\Service\JobService
     */
    private function getJobService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Job');
    }

    public function setRespAction()
    {
        $dayId = $this->params()->fromPost('day_id');
        $jobId = $this->params()->fromPost('job_id');

        $respUserIds = $this->params()->fromPost('resp_user_ids');

        $day = $this->getDayRepository()->find($dayId);
        $job = $this->getJobRepository()->find($jobId);

        $users = array();
        if (is_array($respUserIds)) {
            foreach ($respUserIds as $respUserId) {
                $users[$respUserId] = $this->getUserRepository()->find($respUserId);
            }
        }

        $this->getJobService()->setResponsableUsers($job, $day, $users);

        $viewModel = new ViewModel(array(
            'camp' => $day->getCamp(),
            'day' => $day,
            'job' => $job,
        ));
        $viewModel->setTemplate('ecamp-web/camp/job/job-resp-select');

        return $viewModel;
    }

}
