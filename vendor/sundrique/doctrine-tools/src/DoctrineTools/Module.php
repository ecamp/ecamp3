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

namespace DoctrineTools;

use Zend\ModuleManager\Feature;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Mvc\MvcEvent;
use DoctrineTools\Component\Console\Input\StringInput;
use DoctrineTools\Component\Console\Output\PropertyOutput;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;

/**
 * Doctrine Tools module
 *
 * @license MIT
 * @author Aleksandr Sandrovskiy <a.sandrovsky@gmail.com>
 */
class Module implements Feature\AutoloaderProviderInterface, Feature\ConfigProviderInterface {

	private $serviceManager;

	/**
	 * {@inheritDoc}
	 */
	public function onBootstrap(MvcEvent $event) {
		$this->serviceManager = $event->getApplication()->getServiceManager();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAutoloaderConfig() {
		return array(
			AutoloaderFactory::STANDARD_AUTOLOADER => array(
				StandardAutoloader::LOAD_NS => array(
					__NAMESPACE__ => __DIR__,
				),
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfig() {
		return include __DIR__ . '/../../config/module.config.php';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConsoleUsage(Console $console) {
		$input = new StringInput('list');
		$output = new PropertyOutput();

		$cli = $this->serviceManager->get('doctrinetools.console_application');

		$cli->run($input, $output);

		return $output->getMessage();
	}
}