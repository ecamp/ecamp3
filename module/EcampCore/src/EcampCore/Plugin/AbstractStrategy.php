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

/**
 * This is the base class for all plugin strategies
 *
 * Every pluginstrategy is *only* responsible for rendering a plugin and declaring some basic
 * support, but *not* for updating its configuration etc. For this purpose, use controllers
 * and models.
 */

namespace EcampCore\Plugin;

use EcampCore\Entity\Medium;
use EcampCore\Entity\EventPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractStrategy
{
    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \EcampCore\Entity\EventPlugin
     */
    private $eventPlugin;

    /**
     * @var \EcampCore\Entity\Medium
     */
    private $medium;

    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        EntityManagerInterface $entityManager,
        EventPlugin $eventPlugin,
        Medium $medium
    ){
        $this->serviceLocator = $serviceLocator;
        $this->entityManager = $entityManager;
        $this->eventPlugin = $eventPlugin;
        $this->medium = $medium;
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return \EcampCore\Plugin\AbstractStrategy
     */
    protected function persist($object)
    {
        $this->entityManager->persist($object);

        return $this;
    }

    /**
     * @return \EcampCore\Plugin\AbstractStrategy
     */
    protected function remove($object)
    {
        $this->entityManager->remove($object);

        return $this;
    }

    public function getId()
    {
        return $this->eventPlugin->getId();
    }

    /**
     * @return \EcampCore\Entity\EventPlugin
     */
    protected function getEventPlugin()
    {
        return $this->eventPlugin;
    }

    /**
     * Deletes the EventPlugin from the Database.
     * This method should be overritten, if a Plugin requires
     * special handling while deleting the EventPlugin Instance
     */
    protected function deleteEventPlugin()
    {
        $this->remove($this->eventPlugin);
    }

    /**
     * @return \EcampCore\Entity\Medium
     */
    protected function getMedium()
    {
        return $this->medium;
    }

    /**
     * @return \EcampCore\Entity\Camp
     */
    protected function getCamp()
    {
        return $this->getEventPlugin()->getCamp();
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function render()
    {
        $viewModel = $this->createViewModel();
        $viewModel->setVariable('eventPlugin', $this->getEventPlugin());
        $viewModel->setVariable('camp', $this->getCamp());

        return $viewModel;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    abstract protected function createViewModel();

    abstract public function getTitle();
}
