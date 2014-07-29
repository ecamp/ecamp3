<?php
/*
 * Copyright (C) 2011 Urban Suppiger
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace EcampWeb\Controller\Camp;

use Zend\Form\FormInterface;
use Zend\Mvc\MvcEvent;
use EcampLib\Validation\ValidationException;

class PeriodController
    extends BaseController
{
    /**
     * @return \EcampCore\Repository\PeriodRepository
     */
    private function getPeriodRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Period');
    }

    /**
     * @return \EcampCore\Service\PeriodService
     */
    private function getPeriodService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Period');
    }

    public function onDispatch( MvcEvent $e )
    {
        /* move these 3 lines to a more general location */
        $sm  = $this->getServiceLocator();
        $exceptionstrategy = $sm->get('ViewManager')->getExceptionStrategy();
        $exceptionstrategy->setExceptionTemplate('error/ajaxform');

        parent::onDispatch($e);
    }

    public function addPeriodAction()
    {
        $form = $this->createForm('EcampWeb\Form\Period\PeriodCreateForm');
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Period', 'action' => 'addPeriod')
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();

                try {
                    $this->getPeriodService()->Create($this->getCamp(), $data);
                    $this->flashMessenger()->addSuccessMessage('Period successfully created.');

                    return $this->getRedirectResponse(
                        $this->url()->fromRoute('web/camp/default', array('camp' => $this->getCamp()))
                    );

                } catch (ValidationException $ex) {
                    $form->setMessages($ex->getValidationMessages());
                }
            }
        }

        return array('form' => $form);
    }

    public function editPeriodAction()
    {
        $periodId = $this->params()->fromQuery('periodId');
        $period = $this->getPeriodRepository()->find($periodId);
        if(!$period ) throw new \Exception('Period not found.');

        $form = $this->createForm('EcampWeb\Form\Period\PeriodEditForm'); // new \EcampWeb\Form\Period\PeriodEditForm($period);
        $form->bind($period);
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Period', 'action' => 'editPeriod'),
                array('query' => array('periodId' => $periodId))
            )
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData(FormInterface::VALUES_AS_ARRAY);

                try {
                    $this->getPeriodService()->Update($period, $data);
                    $this->flashMessenger()->addSuccessMessage('Period successfully updated.');

                    return $this->getRedirectResponse(
                        $this->url()->fromRoute('web/camp/default', array('camp' => $this->getCamp()))
                    );

                } catch (ValidationException $ex) {
                    $form->setMessages($ex->getValidationMessages());
                }
            }
        }

        return array('form' => $form);
    }

    public function deletePeriodAction()
    {
        $periodId = $this->params()->fromQuery('periodId');
        $period = $this->getPeriodRepository()->find($periodId);

        try {
            if ($period == null) {
                throw new \Exception('period not found');
            }

            $this->getPeriodService()->Delete($period);
            $this->flashMessenger()->addSuccessMessage('Period successfully deleted');

        } catch (\Exception $ex) {
            $this->flashMessenger()->addErrorMessage('Period can not be deleted - ' . $ex->getMessage());
        }

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp())
        );
    }

}
