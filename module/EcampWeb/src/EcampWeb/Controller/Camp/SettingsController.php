<?php

namespace EcampWeb\Controller\Camp;

use EcampLib\Validation\ValidationException;
use Zend\Form\FormInterface;
use Zend\Http\PhpEnvironment\Response;

class SettingsController extends BaseController
{

    /**
     * @return \EcampCore\Repository\EventCategoryRepository
     */
    private function getEventCategoryRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventCategory');
    }

    /**
     * @return \EcampCore\Repository\JobRepository
     */
    private function getJobRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Job');
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


    public function indexAction()
    {
    }


    public function createEventCategoryAction()
    {
        $camp = $this->getCamp();

        /** @var \EcampWeb\Form\EventCategory\EventCategoryForm $form */
        $form = $this->createForm('EcampWeb\Form\EventCategory\EventCategoryForm');
        $form->setCampTypeId($camp->getCampType()->getId());
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Settings', 'action' => 'createEventCategory')
            )
        );

        if($this->getRequest()->isPost()){
            $form->setData($this->params()->fromPost());

            if($form->isValid()){
                $data = $form->getData();

                try {
                    $eventCategoryData = $data['event-category'];

                    try {
                        $this->getEventCategoryService()->Create($camp, $eventCategoryData);

                        return $this->getRedirectResponse(
                            $this->url()->fromRoute(
                                'web/camp/default',
                                array('camp' => $this->getCamp(), 'controller' => 'Settings')
                            )
                        );
                    } catch (ValidationException $ex) {
                        throw ValidationException::FromInnerException('event-category', $ex);
                    }
                } catch (ValidationException $ex) {
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                    $form->setMessages($ex->getValidationMessages());
                } catch (\Exception $ex) {
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                }

            }
        }

        return array('form' => $form);
    }


    public function editEventCategoryAction()
    {
        $eventCategoryId = $this->params()->fromQuery('eventCategoryId');
        $eventCategory = $this->getEventCategoryRepository()->find($eventCategoryId);
        if(!$eventCategory ) throw new \Exception('EventCategory not found.');

        $form = $this->createForm('EcampWeb\Form\EventCategory\EventCategoryForm');
        $form->bind($eventCategory);
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Settings', 'action' => 'editEventCategory'),
                array('query' => array('eventCategoryId' => $eventCategoryId))
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData(FormInterface::VALUES_AS_ARRAY);

                try {
                    $eventCategoryData = $data['event-category'];

                    try {
                        $this->getEventCategoryService()->Update($eventCategory, $eventCategoryData);

                        return $this->getRedirectResponse(
                            $this->url()->fromRoute(
                                'web/camp/default',
                                array('camp' => $this->getCamp(), 'controller' => 'Settings')
                            )
                        );
                    } catch (ValidationException $ex) {
                        throw ValidationException::FromInnerException('event-category', $ex);
                    }
                } catch (ValidationException $ex) {
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                    $form->setMessages($ex->getValidationMessages());
                }
            }
        }

        return array('form' => $form);
    }


    public function deleteEventCategoryAction()
    {
        $eventCategoryId = $this->params()->fromQuery('eventCategoryId');
        $eventCategory = $this->getEventCategoryRepository()->find($eventCategoryId);
        if(!$eventCategory ) throw new \Exception('EventCategory not found.');

        $form = $this->createForm('EcampLib\Form\BaseForm');
        $form->setName('event-category');
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Settings', 'action' => 'deleteEventCategory'),
                array('query' => array('eventCategoryId' => $eventCategoryId))
            )
        );

        if ($this->getRequest()->isPost()) {
            try {
                $this->getEventCategoryService()->Delete($eventCategory);

                return $this->getRedirectResponse(
                    $this->url()->fromRoute(
                        'web/camp/default',
                        array('camp' => $this->getCamp(), 'controller' => 'Settings')
                    )
                );
            } catch(\Exception $ex) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return array('form' => $form, 'eventCategory' => $eventCategory);
    }



    public function createJobAction()
    {
        $camp = $this->getCamp();

        /** @var \EcampWeb\Form\Job\JobForm $form */
        $form = $this->createForm('EcampWeb\Form\Job\JobForm');
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Settings', 'action' => 'createJob')
            )
        );

        if($this->getRequest()->isPost()){
            $form->setData($this->params()->fromPost());

            if($form->isValid()){
                $data = $form->getData();

                try {
                    $jobData = $data['job'];

                    try {
                        $this->getJobService()->Create($camp, $jobData);

                        return $this->getRedirectResponse(
                            $this->url()->fromRoute(
                                'web/camp/default',
                                array('camp' => $this->getCamp(), 'controller' => 'Settings')
                            )
                        );
                    } catch (ValidationException $ex) {
                        throw ValidationException::FromInnerException('job', $ex);
                    }
                } catch (ValidationException $ex) {
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                    $form->setMessages($ex->getValidationMessages());
                } catch (\Exception $ex) {
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                }

            }
        }

        return array('form' => $form);
    }

    public function editJobAction()
    {
        $jobId = $this->params()->fromQuery('jobId');
        $job = $this->getJobRepository()->find($jobId);
        if(!$job) throw new \Exception('Job not found.');

        $form = $this->createForm('EcampWeb\Form\Job\JobForm');
        $form->bind($job);
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Settings', 'action' => 'editJob'),
                array('query' => array('jobId' => $jobId))
            )
        );

        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if($form->isValid()) {
                $data = $form->getData(FormInterface::VALUES_AS_ARRAY);

                try {
                    $jobData = $data['job'];

                    try {
                        $this->getJobService()->Update($job, $jobData);

                        return $this->getRedirectResponse(
                            $this->url()->fromRoute(
                                'web/camp/default',
                                array('camp' => $this->getCamp(), 'controller' => 'Settings')
                            )
                        );
                    } catch (ValidationException $ex) {
                        throw ValidationException::FromInnerException('job', $ex);
                    }
                } catch (ValidationException $ex) {
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
                    $form->setMessages($ex->getValidationMessages());
                }
            }
        }

        return array('form' => $form);
    }


    public function deleteJobAction()
    {
        $jobId = $this->params()->fromQuery('jobId');
        $job = $this->getJobRepository()->find($jobId);
        if(!$job) throw new \Exception('Job not found.');

        $form = $this->createForm('EcampLib\Form\BaseForm');
        $form->setName('job');
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Settings', 'action' => 'deleteJob'),
                array('query' => array('jobId' => $jobId))
            )
        );

        if ($this->getRequest()->isPost()) {
            try {
                $this->getJobService()->Delete($job);

                return $this->getRedirectResponse(
                    $this->url()->fromRoute(
                        'web/camp/default',
                        array('camp' => $this->getCamp(), 'controller' => 'Settings')
                    )
                );
            } catch(\Exception $ex) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return array('form' => $form, 'job' => $job);
    }

}