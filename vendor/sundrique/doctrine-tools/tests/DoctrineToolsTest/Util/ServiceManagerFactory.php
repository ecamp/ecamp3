<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace DoctrineToolsTest\Util;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

/**
 * Factory to build configured service manager for testing purpose
 *
 * @license MIT
 * @author Aleksandr Sandrovskiy <a.sandrovsky@gmail.com>
 */
class ServiceManagerFactory {

	/**
	 * @var
	 */
	protected static $config;

	/**
	 * @param array $config
	 */
	public static function setConfig(array $config) {
		static::$config = $config;
	}

	/**
	 * @return \Zend\ServiceManager\ServiceManager
	 */
	public static function getServiceManager() {
		$serviceManager = new ServiceManager(new ServiceManagerConfig(
			isset(static::$config['service_manager']) ? static::$config['service_manager'] : array()
		));
		$serviceManager->setService('ApplicationConfig', static::$config);
		$serviceManager->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');

		$moduleManager = $serviceManager->get('ModuleManager');
		$moduleManager->loadModules();
		return $serviceManager;
	}
}
