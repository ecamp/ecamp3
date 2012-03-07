<?php

namespace CoreApi\Service;

class Avatar extends ServiceAbstract
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
	
	/**
	* Setup ACL. Is used for manual calls of 'checkACL' and for automatic checking
	* @see    CoreApi\Service\ServiceAbstract::_setupAcl()
	* @return void
	*/
	protected function _setupAcl()
	{
		$this->_acl->allow('user_me', $this, 'getUserAvatar');
		$this->_acl->allow('user_me', $this, 'getGroupAvatar');
		$this->_acl->allow('user_me', $this, 'sendUserAvatar');
		$this->_acl->allow('user_me', $this, 'sendGroupAvatar');
	}
	
	
	protected function getUserAvatar($id)
	{
		$user = $this->userService->get($id);
		
		if( $user->getImageData() == null )
		{
			$imageData = file_get_contents(APPLICATION_PATH . "/../public/img/default_avatar.png");
			$imageMime = "image/png";
		}
		else
		{
			$imageData = $user->getImageData();
			$imageMime = $user->getImageMime();
		}
		
		return array($imageData, $imageMime);
	}
	
	
	protected function getGroupAvatar($id)
	{
		$group = $this->groupService->get($id);
		
		if($group->getImageData() == null)
		{
			$imageData = file_get_contents(APPLICATION_PATH . "/../public/img/default_group.png");
			$imageMime = "image/png";
		}
		else
		{
			$imageData = $group->getImageData();
			$imageMime = $group->getImageMime();
		}
		
		return array($imageData, $imageMime);
	}
	
	
	protected function sendUserAvatar($id)
	{
		list($imageData, $imageMime) = $this->getUserAvatar($id);
		$response = \Zend_Controller_Front::getInstance()->getResponse();
			
		$response->setHeader("Content-type", $imageMime);
		$response->setBody($imageData);
		
		$user = $this->userService->get($id);
		$this->cacheme($user->getUpdatedAt()->getTimestamp());
	}

	
	protected function sendGroupAvatar($id)
	{		
		list($imageData, $imageMime) = $this->getGroupAvatar($id);
		$response = \Zend_Controller_Front::getInstance()->getResponse();
	
		$response->setHeader("Content-type", $imageMime);
		$response->setBody($imageData);
		
		$group = $this->groupService->get($id);
		$this->cacheme($group->getUpdatedAt()->getTimestamp());
	}
	
	
	private function cacheme($lastmodified)
	{
		$response = \Zend_Controller_Front::getInstance()->getResponse();
				
		// no cache if error found or if response
		// is not a Zend_Controller_Response_Http instance
		if (!($response instanceOf Zend_Controller_Response_Http) || $response->isException()) 
		{
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
		if (!$PageWasUpdated or $DoIDsMatch)
		{
	
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
	
		} 
		else 
		{
		
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