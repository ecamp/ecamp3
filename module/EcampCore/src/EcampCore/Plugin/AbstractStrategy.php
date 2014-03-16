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
use EcampCore\Entity\Plugin;
use EcampCore\Entity\Event;
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

    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        EntityManagerInterface $entityManager
    ){
        $this->serviceLocator = $serviceLocator;
        $this->entityManager = $entityManager;
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

    /**
     * @param  Event       $event
     * @param  Plugin      $plugin
     * @return EventPlugin
     */
    public function create(Event $event, Plugin $plugin)
    {
        $eventPlugin = new EventPlugin($event, $plugin, $plugin->getName());
        $this->persist($eventPlugin);

        return $eventPlugin;
    }

    /**
     * Deletes the EventPlugin from the Database.
     * This method should be overritten, if a Plugin requires
     * special handling while deleting the EventPlugin Instance
     */
    public function delete(EventPlugin $eventPlugin)
    {
        $this->remove($eventPlugin);
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function render(EventPlugin $eventPlugin, Medium $medium)
    {
        $viewModel = $this->createViewModel($eventPlugin, $medium);
        $viewModel->setVariable('eventPlugin', $eventPlugin);
        $viewModel->setVariable('camp', $eventPlugin->getCamp());

        return $viewModel;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    abstract protected function createViewModel(EventPlugin $eventPlugin, Medium $medium);

    abstract public function getTitle(EventPlugin $eventPlugin);
}
