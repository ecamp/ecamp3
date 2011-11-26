<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
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

class WebApp_AvatarController extends \Zend_Controller_Action
{
	
	/**
	 * @var \CoreApi\Service\User
	 * @Inject \CoreApi\Service\User
	 */
	protected $userService;
	
	/**
	 * @var \CoreApi\Service\Group
	 * @Inject \CoreApi\Service\Group
	 */
	protected $groupService;
	

	
	public function init()
	{
		\Zend_Registry::get('kernel')->InjectDependencies($this);
	}

	public function userAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam("id");
		$user = $this->userService->get($id);
		
		if( $user->getImageData() == null ) {
			//$this->_redirect('img/default_avatar.png');

			/* this is faster than redirecting the browser */
			$this->getResponse()->setHeader("Content-type", "image/png");
			$this->getResponse()->setBody(file_get_contents(APPLICATION_PATH . "/../public/img/default_avatar.png"));
		} else {
			$this->getResponse()->setHeader("Content-type", $user->getImageMime());
			$this->getResponse()->setBody($user->getImageData());
		}

		$this->cacheme($user->getUpdatedAt()->getTimestamp());
	}

	public function groupAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam("id");
	    $group = $this->groupService->get($id);
		
		if( $group->getImageData() == null ) {
			// $this->_redirect("img/default_group.png");

			/* this is faster than redirecting the browser */
			$this->getResponse()->setHeader("Content-type", "image/png");
			$this->getResponse()->setBody(file_get_contents(APPLICATION_PATH . "/../public/img/default_group.png"));

		} else {
			$this->getResponse()->setHeader("Content-type", $group->getImageMime());
			$this->getResponse()->setBody($group->getImageData());
		}

		$this->cacheme($group->getUpdatedAt()->getTimestamp());
	}

	protected function cacheme($lastmodified)
    {
	    $response = $this->getResponse();

        // no cache if error found or if response
		// is not a Zend_Controller_Response_Http instance
        if (!($response instanceOf Zend_Controller_Response_Http) || $response->isException()) {
            return;
        }
         // Generate unique Hash-ID by using MD5
        $hashID = md5($response->getBody());

        // Specify the time when the page has
        // been changed. For example this date
		// can come from the database or any
        // file. Here we define a fixed date value:
        $lastChangeTime = $lastmodified;

        // Define the proxy or cache expire time
		$expireTime = 3600; // seconds (= one hour)

        // Get request headers:
        $headers = apache_request_headers();
        // you could also use getallheaders() or $_SERVER
		// or HTTP_SERVER_VARS

        // Set cache/proxy informations:
	    header("Cache-Control:");
	    header("Expires:");
	    header("Pragma:");
	    header("Etag:");
	    header("Last-Modified:");

        $response->setHeader('Cache-Control', 'max-age=' . $expireTime); // must-revalidate
        $response->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + $expireTime) . ' GMT');
        // Set last modified (this helps search engines
        // and other web tools to determine if a page has
        // been updated)
        $response->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $lastChangeTime) . ' GMT');
        // Send a "ETag" which represents the content
        // (this helps browsers to determine if the page
        // has been changed or if it can be loaded from
        // the cache - this will speed up the page loading)
		$response->setHeader('ETag', $hashID);

        // The browser "asks" us if the requested page has
        // been changed and sends the last modified date he
        // has in it's internal cache. So now we can check
		// if the submitted time equals our internal time value.
        // If yes then the page did not get updated
        $PageWasUpdated = !(isset($headers['If-Modified-Since']) && strtotime($headers['If-Modified-Since']) == $lastChangeTime);

        // The second possibility is that the browser sends us
		// the last Hash-ID he has. If he does we can determine
        // if he has the latest version by comparing both IDs.
        // Warning: If-None-Match header can have a value like "hash0, hash1"
        $DoIDsMatch = (isset($headers['If-None-Match']) && strpos($headers['If-None-Match'], $hashID) !== false);

		// Does one of the two ways apply?
        if (!$PageWasUpdated or $DoIDsMatch){

            // Okay, the browser already has the
            // latest version of our page in his
			// cache. So just tell him that
            // the page was not modified and DON'T
            // send the content -> this saves bandwith and
            // speeds up the loading for the visitor
            $response->setHttpResponseCode(304);
            // That's all, now close the connection:
            $response->setHeader('Connection', 'close');

            // The magical part:
			// No content here ;-)
            $front = Zend_Controller_Front::getInstance();
            $front->returnResponse(false);

            // Just the headers
			$response->sendHeaders();
			exit();

        } else {

            // Okay, the browser does not have the
			// latest version or does not have any
            // version cached. So we have to send him
            // the full page.
            $response->setHttpResponseCode(200);

			// Tell the browser which size the content
            $response->setHeader('Content-Length', mb_strlen($response));

        }
    }
}