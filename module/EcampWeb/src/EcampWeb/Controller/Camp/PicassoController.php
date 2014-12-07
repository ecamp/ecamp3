<?php
/**
 * Created by PhpStorm.
 * User: pirmin
 * Date: 26.10.14
 * Time: 11:43
 */

namespace EcampWeb\Controller\Camp;

use EcampLib\Validation\ValidationException;
use EcampWeb\Form\Event\EventCreateForm;
use EcampWeb\Form\Event\EventUpdateForm;
use Zend\Http\PhpEnvironment\Response;

class PicassoController
    extends BaseController
{
    /**
     * @return \EcampCore\Repository\DayRepository
     */
    private function getDayRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Day');
    }

    /**
     * @return \EcampCore\Repository\PeriodRepository
     */
    private function getPeriodRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Period');
    }

    /**
     * @return \EcampCore\Repository\EventInstanceRepository
     */
    private function getEventInstanceRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\EventInstance');
    }

    /**
     * @return \EcampCore\Service\EventService
     */
    private function getEventService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Event');
    }

    /**
     * @return \EcampCore\Service\EventInstanceService
     */
    private function getEventInstanceService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\EventInstance');
    }

    public function indexAction()
    {
        $this->setFullHeight();

        return array(
            'camp' => $this->getCamp()
        );
    }

    public function createEventAction()
    {
        $periodId = $this->params()->fromQuery('periodId');

        /** @var \EcampCore\Entity\Period $period */
        $period = $this->getPeriodRepository()->find($periodId);
        $camp = $period->getCamp();

        $form = new EventCreateForm($camp, $period);
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $camp, 'controller' => 'Picasso', 'action' => 'createEvent'),
                array('query' => array('periodId' => $periodId))
            )
        );

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {

                try {
                    try {
                        $eventData = $data['event'];
                        $event = $this->getEventService()->Create($camp, $eventData);
                    } catch (ValidationException $e) {
                        throw ValidationException::FromInnerException('event', $e);
                    }

                    try {
                        $eventInstanceData = $data['eventInstance'];
                        $this->getEventInstanceService()->Create($event, $eventInstanceData);
                    } catch (ValidationException $e) {
                        throw ValidationException::FromInnerException('eventInstance', $e);
                    }

                    $this->flashMessenger()->addSuccessMessage('Event created');
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_204);

                } catch (ValidationException $e) {
                    $form->extractFromException($e);
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Event not created');
                    throw $e;
                }

            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }

        } else {
            $data = $this->getRequest()->getQuery();
            $form->setData($data);
        }

        return array(
            'form' => $form
        );
    }


    public function updateEventInstanceAction(){
        $eventInstanceId = $this->params()->fromQuery('eventInstanceId');

        /** @var \EcampCore\Entity\EventInstance $eventInstance */
        $eventInstance = $this->getEventInstanceRepository()->find($eventInstanceId);
        $event = $eventInstance->getEvent();
        $camp = $this->getCamp();

        $form = new EventUpdateForm($camp, $event, $eventInstance);
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $camp, 'controller' => 'Picasso', 'action' => 'updateEventInstance'),
                array('query' => array('eventInstanceId' => $eventInstanceId))
            )
        );

        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {

                try {
                    try {
                        $eventData = $data['event'];
                        $this->getEventService()->Update($event, $eventData);
                    } catch (ValidationException $e) {
                        throw ValidationException::FromInnerException('event', $e);
                    }

                    try {
                        $eventInstanceData = $data['eventInstance'];
                        $this->getEventInstanceService()->Update($eventInstance, $eventInstanceData);
                    } catch (ValidationException $e) {
                        throw ValidationException::FromInnerException('eventInstance', $e);
                    }

                    $this->flashMessenger()->addSuccessMessage('Event created');
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_204);

                } catch (ValidationException $e) {
                    $form->extractFromException($e);
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Event not created');
                    throw $e;
                }

            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }

        } else {
            $data = $this->getRequest()->getQuery();
            $form->setData($data);
        }

        return array(
            'form' => $form
        );
    }
}
