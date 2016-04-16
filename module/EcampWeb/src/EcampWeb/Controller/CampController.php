<?php

namespace EcampWeb\Controller;

use EcampLib\Form\WizardForm;
use EcampLib\Validation\ValidationException;
use EcampWeb\Form\Camp\CampCreateWizard;
use Zend\Http\PhpEnvironment\Response;
use Zend\View\Model\ViewModel;

class CampController extends BaseController
{
    /**
     * @return \EcampCore\Repository\CampOwnerRepository
     */
    private function getCampOwnerRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\AbstractCampOwner');
    }

    /**
     * @return \EcampCore\Service\PeriodService
     */
    private function getPeriodService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Period');
    }

    /**
     * @return \EcampCore\Service\EventCategoryService
     */
    private function getEventCategoryService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\EventCategory');
    }

    /**
     * @return \EcampCore\Service\JobService
     */
    private function getJobService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Job');
    }

    public function createAction()
    {
        /** @var \EcampLib\Form\WizardForm $wizard */
        $wizard = $this->createForm('EcampWeb\Form\Camp\CampCreateWizard');
        $wizard->setAction($this->url()->fromRoute('web/default', array('controller' => 'Camp', 'action' => 'create')));
        $wizard->setStep(WizardForm::FIRST_STEP);

        $ownerId = $this->getRequest()->getQuery('owner');
        if ($ownerId != null) {
            $owner = $this->getCampOwnerRepository()->find($ownerId);
            $wizard->setStepData(CampCreateWizard::STEP_CAMP_DETAILS, array('owner' => $owner));
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($wizard->setData($data)->isValid()) {
                $wizard->setStep($this->getRequest()->getQuery('step', WizardForm::NEXT_STEP));

                if ($wizard->isComplete()) {
                    try {

                        try {
                            $campData = $wizard->getStepData(CampCreateWizard::STEP_CAMP_DETAILS);
                            $camp = $this->getCampService()->Create($campData);
                        } catch (ValidationException $ex) {
                            $wizard->setStep(CampCreateWizard::STEP_CAMP_DETAILS);
                            throw ValidationException::FromInnerException(CampCreateWizard::STEP_CAMP_DETAILS, $ex);
                        }

                        $periodsData = $wizard->getStepData(CampCreateWizard::STEP_CAMP_PERIODS, array());
                        try {
                            foreach ($periodsData as $idx => $periodData) {
                                try {
                                    $this->getPeriodService()->Create($camp, $periodData);
                                } catch (ValidationException $ex) {
                                    throw ValidationException::FromInnerException($idx, $ex);
                                }
                            }
                        } catch (ValidationException $ex) {
                            $wizard->setStep(CampCreateWizard::STEP_CAMP_PERIODS);
                            throw ValidationException::FromInnerException(CampCreateWizard::STEP_CAMP_PERIODS, $ex);
                        }

                        $categoriesData = $wizard->getStepData(CampCreateWizard::STEP_CAMP_EVENT_CATEGORIES, array());
                        try {
                            foreach ($categoriesData as $idx => $categoryData) {
                                try {
                                    $this->getEventCategoryService()->Create($camp, $categoryData);
                                } catch (ValidationException $ex) {
                                    throw ValidationException::FromInnerException($idx, $ex);
                                }
                            }
                        } catch (ValidationException $ex) {
                            $wizard->setStep(CampCreateWizard::STEP_CAMP_EVENT_CATEGORIES);
                            throw ValidationException::FromInnerException(CampCreateWizard::STEP_CAMP_EVENT_CATEGORIES, $ex);
                        }

                        $jobsData = $wizard->getStepData(CampCreateWizard::STEP_CAMP_JOBS, array());
                        try {
                            foreach ($jobsData as $idx => $jobData) {
                                try {
                                    $this->getJobService()->Create($camp, $jobData);
                                } catch (ValidationException $ex) {
                                    throw ValidationException::FromInnerException($idx, $ex);
                                }
                            }
                        } catch (ValidationException $ex) {
                            $wizard->setStep(CampCreateWizard::STEP_CAMP_JOBS);
                            throw ValidationException::FromInnerException(CampCreateWizard::STEP_CAMP_JOBS, $ex);
                        }

                        return $this->emptyResponse();
                    } catch (ValidationException $ex) {
                        $wizard->setMessages($ex->getValidationMessages());
                        $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                    } catch (\Exception $ex) {
                        $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                    }
                }

            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('size', 'lg');
        $viewModel->setVariable('form', $wizard->createForm());
        $viewModel->setTemplate('ecamp-web/camp/create/' . $wizard->getStepName());

        return $viewModel;
    }

}
