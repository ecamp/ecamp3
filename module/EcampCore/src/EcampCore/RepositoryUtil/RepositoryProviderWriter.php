<?php

namespace EcampCore\RepositoryUtil;

class RepositoryProviderWriter
{
	
	private $repositoryProvider =
'<?php

namespace EcampCore\RepositoryUtil;

use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryProvider 
{
	/** @var ServiceLocatorInterface */
	private $serviceLocator;
	
	
	public function __construct(ServiceLocatorInterface $serviceLocator){
		$this->serviceLocator = $serviceLocator;
	}
	
	<<REPOSITORY-METHODS>>
}
	';
	
	private $repositoryMethod =
'	
	/**
	 * @return <<REPOSITORY-CLASS>> 
	 */
	public function <<REPOSITORY-METHOD>>(){
		return $this->serviceLocator->get(\'<<REPOSITORY-ALIAS>>\');
	}
	';
	
	
	private $repositoryConfig = 
"<?php
return array(
	
	'factories' => array(
<<REPOSITORY-FACTORY>>
	),
	
);";
	
	private $repositoryFactory = 
"		'<<REPOSITORY-ALIAS>>' => new EcampCore\RepositoryUtil\RepositoryFactory('<<ENTITY-CLASS>>'),";
	
	
	public function writeRepositoryProvider(){
	
		$repositoryDir = dirname(__DIR__) . '/Repository/';
		$repositoryProviderFile = __DIR__ . '/RepositoryProvider.php';
	
		$repositorys = array();
	
		foreach(scandir($repositoryDir) as $k => $v){
			if(is_file($repositoryDir . $v)){
				
				$repositoryClass = 'EcampCore\Repository\\' . substr($v, 0, -4);
				$repositoryMethod = lcfirst(substr($v, 0, -4));
				$repositoryAlias = 'ecamp.repo.' . strtolower(substr($v, 0, -14));
	
				$repositorys[] = str_replace(
					array('<<REPOSITORY-CLASS>>', '<<REPOSITORY-METHOD>>', '<<REPOSITORY-ALIAS>>'),
					array($repositoryClass, $repositoryMethod, $repositoryAlias),
					$this->repositoryMethod);
			}
		}
	
		$src = str_replace('<<REPOSITORY-METHODS>>', implode(PHP_EOL, $repositorys), $this->repositoryProvider);
		file_put_contents($repositoryProviderFile, $src);
		
		return $src;
	}
	
	
	public function writeRepositoryConfig(){
		$repositoryDir = dirname(__DIR__) . '/Repository/';
		$repositoryConfigFile = __DIR__ . '/../../../config/service.config.repos.php';
				
		$repositoryFactories = array();
		
		foreach(scandir($repositoryDir) as $k => $v){
			if(is_file($repositoryDir . $v)){
				
				$entityClass = 'EcampCore\Entity\\' . substr($v, 0, -14);
				$repositoryAlias = 'ecamp.repo.' . strtolower(substr($v, 0, -14));
	
				$repositoryFactories[] = str_replace(
					array('<<ENTITY-CLASS>>', '<<REPOSITORY-ALIAS>>'),
					array($entityClass, $repositoryAlias),
					$this->repositoryFactory);
			}
		}
		
		$src = str_replace("<<REPOSITORY-FACTORY>>", implode(PHP_EOL, $repositoryFactories), $this->repositoryConfig);
		file_put_contents($repositoryConfigFile, $src);
		
		return $src;
	}
	
}