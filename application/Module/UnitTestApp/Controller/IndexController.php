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

class UnitTestApp_IndexController extends \Zend_Controller_Action
{

	private $resultFile;
	
    public function init()
    {
		$this->resultFile = APPLICATION_PATH . "/../data/unittest/results.xml";
		
    }

    public function indexAction()
    {
    	$this->view->shortFeedback = "fail";
    	$this->view->testTime = false;
    	$this->view->result = false;
    	
    	 
    	if(file_exists($this->resultFile))
    	{
    		try{
		    	$result_xml = file_get_contents($this->resultFile);
		    	
		    	$result = new SimpleXMLElement($result_xml);
		    	$testTime = filemtime($this->resultFile);
		    	
		    	$shortFeedback = (
		    		$result->testsuite->attributes()->failures == 0 && 
					$result->testsuite->attributes()->errors == 0)
		    			? "pass" : "fail";
		    	
		    	
		    	$this->view->result = $result;
		    	$this->view->testTime = $testTime;
		    	$this->view->shortFeedback = $shortFeedback;
    		}
    		catch(\Exception $e){
    			var_dump($e->getMessage());
    		}
    	}
    }
    
    public function runAction(){
    	$php = PHP_BINDIR . DIRECTORY_SEPARATOR . 'php';
    	
    	exec("cd ../test/UnitTest/bin/ && $php UnitTestRunner.php", $ret);
    	$this->_redirect('/');
    }
}

