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

use EcampCore\Validation\ValidationException;
use Zend\Http\Response;

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

    public function addPeriodAction()
    {
        $form = new \EcampWeb\Form\Period\CreatePeriod();
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Period', 'action' => 'addPeriod')
            )
        );
        $form->setRedirectAfterSuccess(
            $this->url()->fromRoute('web/camp/default', array('camp' => $this->getCamp()))
        );

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {
                try {
                    $period = $this->getPeriodService()->Create($this->getCamp(), $data);
                    $this->flashMessenger()->addSuccessMessage('Period successfully created.');

                    return $this->emptyResponse();

                } catch (ValidationException $e) {
                    $e->pushToForm($form);
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Error while creating Period.');

                    return $this->emptyResponse();
                }
            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
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

        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('Period can not be deleted');
        }

        return $this->redirect()->toRoute(
            'web/camp/default', array('camp' => $this->getCamp())
        );
    }

    public function movePeriodAction()
    {
        $periodId = $this->params()->fromQuery('periodId');
        $period = $this->getPeriodRepository()->find($periodId);

        $form = new \EcampWeb\Form\Period\MovePeriod();
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Period', 'action' => 'movePeriod'),
                array('query' => array('periodId' => $periodId))
            )
        );
        $form->setRedirectAfterSuccess(
            $this->url()->fromRoute('web/camp/default', array('camp' => $this->getCamp()))
        );

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();

            if ($form->setData($data)->isValid()) {
                try {
                    $this->getPeriodService()->Move($period, $data);

                    $this->flashMessenger()->addSuccessMessage(
                        'Period moved to the ' . $period->getStart()->format('d.m.Y')
                    );

                    return $this->emptyResponse();

                } catch (ValidationException $e) {
                    $e->pushToForm($form);
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Period not moved');

                       return $this->emptyResponse();
                }
            } else {
                var_dump($form->getMessages());
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        } else {
            $form->setData(array('period-move' => array('start' => $period->getStart())));
        }

        return array('form' => $form);
    }

    public function resizePeriodAction()
    {
        $periodId = $this->params()->fromQuery('periodId');
        $period = $this->getPeriodRepository()->find($periodId);

        $form = new \EcampWeb\Form\Period\ResizePeriod();
        $form->setAction(
            $this->url()->fromRoute(
                'web/camp/default',
                array('camp' => $this->getCamp(), 'controller' => 'Period', 'action' => 'resizePeriod'),
                array('query' => array('periodId' => $periodId))
            )
        );
        $form->setRedirectAfterSuccess(
            $this->url()->fromRoute('web/camp/default', array('camp' => $this->getCamp()))
        );

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $data['period-size']['start'] = $period->getStart();

            if ($form->setData($data)->isValid()) {
                try {
                    $this->getPeriodService()->Resize($period, $data);

                    $this->flashMessenger()->addSuccessMessage('Period successfully resized');
                } catch (ValidationException $e) {
                    $e->pushToForm($form);
                    $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage('Period not resized');

                       return $this->emptyResponse();
                }

            } else {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);
            }
        } else {
            $form->setData(array('period-size' => array(
                'start' => $period->getStart(),
                'end' => $period->getEnd()
            )));
        }

        return array('form' => $form);
    }
}
