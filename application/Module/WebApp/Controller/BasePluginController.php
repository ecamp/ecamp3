<?php/* * Copyright (C) 2011 Urban Suppiger * * This file is part of eCamp. * * eCamp is free software: you can redistribute it and/or modify * it under the terms of the GNU Affero General Public License as published by * the Free Software Foundation, either version 3 of the License, or * (at your option) any later version. * * eCamp is distributed in the hope that it will be useful, * but WITHOUT ANY WARRANTY; without even the implied warranty of * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the * GNU Affero General Public License for more details. * * You should have received a copy of the GNU Affero General Public License * along with eCamp.  If not, see <http://www.gnu.org/licenses/>. */ namespace WebApp\Controller;class BasePluginController extends BaseController{	// TODO: remove em		/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;	/**
	 * @var CoreApi\Service\EventService
	 * @Inject CoreApi\Service\EventService
	 */
	protected $eventService;	public function init()	{		parent::init();				/* TO DO: check access rights here */	}}