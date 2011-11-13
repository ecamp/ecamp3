<?php
/**
 *
 * Copyright (C) 2011 pirminmattmann
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
 *
 */



class SearchController
	extends \WebApp\Controller\BaseController
{

	/**
	 * @var Service\SearchUserService
	 * @Inject Service\SearchUserService
	 */
	private $searchUserService;


	public function searchuserAction()
	{
		$query = $this->getRequest()->getParam('query');

		$users = $this->searchUserService->SearchForUser($query);


		
		$this->view->users = $users;

	}

}
