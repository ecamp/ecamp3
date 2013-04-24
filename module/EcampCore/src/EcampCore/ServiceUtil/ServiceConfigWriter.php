<?php

namespace EcampCore\ServiceUtil;

use Zend\Code\Reflection\FileReflection;

use Zend\Config\Config;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceConfigWriter
{
	
	
	private $serviceConfig = 
"<?php
return array(
	
	'aliases' => array(
<<SERVICE-ALIASES>>
	),

	'factories' => array(
<<SERVICE-FACTORY>>
	),

    'invokables' => array(
<<SERVICE-INVOKABLES>>
    ),
    
);";
	
	private $serviceAlias = 
"		'__services__.<<SERVICE-METHOD>>' => '<<SERVICE-ALIAS>>',";
	
	private $internalServiceAlias = 
"		'__internal_services__.<<SERVICE-METHOD>>' => '<<SERVICE-ALIAS>>',";

	private $serviceFactory = 
"		'<<SERVICE-ALIAS>>' => new EcampCore\ServiceUtil\ServiceFactory('<<INTERNAL-SERVICE-ALIAS>>'),";
	
	private $serviceInvokable = 
"		'<<INTERNAL-SERVICE-ALIAS>>' => '<<SERVICE-CLASS>>',";
	
	
	
	/**
	 * @var Zend\ServiceManager\ServiceLocatorInterface
	 */
	private $serviceLocator;
	
	
	public function __construct(ServiceLocatorInterface $serviceLocator){
		$this->serviceLocator = $serviceLocator;
	}
	
	
	public function writeServiceConfigs(){
	
		$config = new Config($this->serviceLocator->get('config'));
	
		foreach($config->ecamp->modules as $module){
			$this->writeServiceConfig(
					$module->services->services_path,
					$module->services->config_file
			);
		}
	}
	
	public function writeServiceConfig($servicePath, $configFile){
		
		$serviceAliases = array();
		$internalServiceAliases = array();
		$serviceFactories = array();
		$serviceInvokables = array();
		
		foreach(scandir($servicePath) as $k => $v){
		
			if(is_file($servicePath . $v) && $v != 'ServiceBase.php'){
				
				ob_start();
				require_once $servicePath . $v;
				ob_end_clean();
				
				$file = new FileReflection($servicePath . $v);
				$classes = $file->getClasses();
				
				if(count($classes)){
					$class = $classes[0];
					
					$serviceClass = $class->getName();
					$internalServiceAlias = substr(str_replace('\service\\', '.internal.service.', strtolower($serviceClass)), 0, -7);
					$serviceAlias = substr(str_replace('\service\\', '.service.', strtolower($serviceClass)), 0, -7);
					$serviceMethod = lcfirst(str_replace('\Service\\', '_', $serviceClass));
					
					$serviceAliases[] = str_replace(
						array('<<SERVICE-METHOD>>', '<<SERVICE-ALIAS>>'),
						array($serviceMethod, $serviceAlias),
						$this->serviceAlias);
					
					$internalServiceAliases[] = str_replace(
						array('<<SERVICE-METHOD>>', '<<SERVICE-ALIAS>>'),
						array($serviceMethod, $internalServiceAlias),
						$this->internalServiceAlias);
					
					$serviceFactories[] = str_replace(
						array('<<SERVICE-CLASS>>', '<<INTERNAL-SERVICE-ALIAS>>', '<<SERVICE-ALIAS>>'),
						array($serviceClass, $internalServiceAlias, $serviceAlias),
						$this->serviceFactory);
					
					$serviceInvokables[] = str_replace(
						array('<<SERVICE-CLASS>>', '<<INTERNAL-SERVICE-ALIAS>>', '<<SERVICE-ALIAS>>'),
						array($serviceClass, $internalServiceAlias, $serviceAlias),
						$this->serviceInvokable);
				}
			}
		}
		
		$src = str_replace(
			array('<<SERVICE-FACTORY>>', '<<SERVICE-INVOKABLES>>', '<<SERVICE-ALIASES>>'),
			array(
				implode(PHP_EOL, $serviceFactories), implode(PHP_EOL, $serviceInvokables), 
				implode(PHP_EOL, $serviceAliases) . PHP_EOL . PHP_EOL . implode(PHP_EOL, $internalServiceAliases)
			),
			$this->serviceConfig);
		file_put_contents($configFile, $src);
		
		return $src;
	}
	
}