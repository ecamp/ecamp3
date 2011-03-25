<?php
/**
 * Created by JetBrains PhpStorm.
 * User: usu
 * Date: 3/25/11
 * Time: 6:57 PM
 * To change this template use File | Settings | File Templates.
 */
 
class EcampTestCaseWithDb extends EcampTestCase {

	/**
	 * @var \Bisna\Application\Container\DoctrineContainer
	 */
	protected $doctrineContainer;

	protected $em;

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

	public function setUp(){
		parent::setUp();

		$this->doctrineContainer = Zend_Registry::get('doctrine');
		$this->em = $this->doctrineContainer->getEntityManager();

		self::dropSchema($this->doctrineContainer->getConnection()->getParams());
		
		$tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
		$tool->createSchema($this->getClassMetas(APPLICATION_PATH . '/Entity', 'Entity\\'));
	}

	public function tearDown()
	{
		parent::tearDown();
	}
}
