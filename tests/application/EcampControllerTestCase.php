<?php
/**
 * Created by JetBrains PhpStorm.
 * User: usu
 * Date: 3/31/11
 * Time: 9:03 PM
 * To change this template use File | Settings | File Templates.
 */

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

class EcampControllerTestCase
	extends Zend_Test_PHPUnit_ControllerTestCase
{
	protected $application;

	/**
	 * @var \Bisna\Application\Container\DoctrineContainer
	 */
	protected $doctrineContainer;

	protected $em;
	

	public function setUp()
	{
		$this->bootstrap = array($this, 'appBootstrap');
		parent::setUp();

		$this->doctrineContainer = Zend_Registry::get('doctrine');
		$this->em = $this->doctrineContainer->getEntityManager();

		self::dropSchema($this->doctrineContainer->getConnection()->getParams());

		$tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
		$tool->createSchema($this->getClassMetas(APPLICATION_PATH . '/Entity', 'Entity\\'));
	}

	public function appBootstrap()
	{
		$this->application = new Zend_Application(
								APPLICATION_ENV,
								APPLICATION_PATH . '/configs/application.ini'
								);

		$this->application->bootstrap();
	}

	public function tearDown()
	{
		$this->resetRequest();
		$this->resetResponse();
		parent::tearDown();
	}

	public function getClassMetas($path, $namespace){
		$metas = array();
		if( $handle = opendir($path))
		{
			while(false !== ($file = readdir($handle) ))
			{
				if(strstr($file,'.php'))
				{
					list($class) = explode('.', $file);
					$metas[] = $this->doctrineContainer->getEntityManager()->getClassMetadata($namespace . $class);
				}
			}
		}
		return $metas;
	}

	public static function dropSchema($params){
		if( file_exists($params['path'])){
			unlink($params['path']);
		}
	}
}
