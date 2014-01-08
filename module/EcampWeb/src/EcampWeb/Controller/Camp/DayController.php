<?php

namespace EcampWeb\Controller\Camp;

use EcampWeb\Form\Event\EventCreateForm;
use Zend\Http\Response;
use EcampLib\Validation\ValidationException;
class DayController extends BaseController
{
    /**
     * @return \EcampCore\Repository\DayRepository
     */
    private function getDayRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Day');
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
        $dayId = $this->params()->fromQuery('dayId');

        if ($dayId != null) {
            /* @var $day \EcampCore\Entity\Day */
            $day = $this->getDayRepository()->find($dayId);

            $nextDay = $this->getDayRepository()->findNextDay($day);
            $prevDay = $this->getDayRepository()->findPrevDay($day);

            $eventInstances = $this->getEventInstanceRepository()->findByDay($day);
        }

        return array(
            'periods' => $this->getCamp()->getPeriods(),
            'day' => $day,
            'nextDay' => $nextDay,
            'prevDay' => $prevDay,
            'eventInstances' => $eventInstances
        );
    }

    public function addEventAction()
    {
        $dayId = $this->params()->fromQuery('dayId');
        $day = $this->getDayRepository()->find($dayId);

        if ($day != null) {
            $period = $day->getPeriod();
            $camp = $day->getCamp();
        }

        $form = new EventCreateForm($camp, $period, $day );
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Day', 'action' => 'addEvent'),
                array('query' => array('dayId' => $dayId))
            )
        );
        $form->setRedirectAfterSuccess(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Day', 'action' => 'Index'),
                array('query' => array('dayId' => $dayId))
            )
        );

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {

                try {
                    $camp = $this->getCamp();
                    $event = $this->getEventService()->Create($camp, $data);
                    $this->getEventInstanceService()->Create($event, $data);

                    $this->flashMessenger()->addSuccessMessage('Event created');

                    return $this->ajaxSuccssResponse(
                        $this->url()->fromRoute(
                            'web/camp/default',
                            array('camp' => $this->getCamp(), 'controller' => 'Day', 'action' => 'Index'),
                            array('query' => array('dayId' => $dayId))
                        )
                    );

                } catch (ValidationException $e) {
                    $form->extractFromException($e);
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Event not created');
                    throw $e;

                    return $this->emptyResponse();
                }

            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        }

        return array(
            'form' => $form
        );
    }

}
