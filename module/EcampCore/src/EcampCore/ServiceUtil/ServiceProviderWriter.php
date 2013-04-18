<?php

namespace EcampCore\ServiceUtil;

class ServiceProviderWriter
{
	
	private $serviceProvider =
'<?php

namespace EcampCore\ServiceUtil;

use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceProvider 
{
	/** @var ServiceLocatorInterface */
	private $serviceLocator;
	
	private $internal;
	
	
	public function __construct(ServiceLocatorInterface $serviceLocator, $internal = false){
		$this->serviceLocator = $serviceLocator;
		$this->internal = $internal;
	}
	
	<<SERVICE-METHODS>>
}
	';
	
	private $serviceMethod =
'	
	/**
	 * @return <<SERVICE-CLASS>> 
	 */
	public function <<SERVICE-METHOD>>(){
		if($this->internal){
			return $this->serviceLocator->get(\'<<INTERNAL-SERVICE-ALIAS>>\');
		} else {
			return $this->serviceLocator->get(\'<<SERVICE-ALIAS>>\');
		}
	}
	';
	
	
	private $serviceConfig = 
"<?php
return array(
	
	'factories' => array(
<<SERVICE-FACTORY>>
	),

    'invokables' => array(
<<SERVICE-INVOKABLES>>
    ),
    
);";
	
	private $serviceFactory = 
"		'<<SERVICE-ALIAS>>' => new EcampCore\ServiceUtil\ServiceFactory('<<INTERNAL-SERVICE-ALIAS>>'),";
	
	private $serviceInvokable = 
"		'<<INTERNAL-SERVICE-ALIAS>>' => '<<SERVICE-CLASS>>',";
	
	
	public function writeServiceProvider(){
	
		$serviceDir = dirname(__DIR__) . '/Service/';
		$serviceProviderFile = __DIR__ . '/ServiceProvider.php';
	
		$services = array();
		
		foreach(scandir($serviceDir) as $k => $v){
			if(is_file($serviceDir . $v) && $v != 'ServiceBase.php'){
		
				$serviceClass = 'EcampCore\Service\\' . substr($v, 0, -4);
				$serviceMethod = lcfirst(substr($v, 0, -4));
				$internalServiceAlias = 'ecamp.internal.service.' . strtolower(substr($v, 0, -11));
				$serviceAlias = 'ecamp.service.' . strtolower(substr($v, 0, -11));
		
				$services[] = str_replace(
					array('<<SERVICE-CLASS>>', '<<SERVICE-METHOD>>', '<<INTERNAL-SERVICE-ALIAS>>', '<<SERVICE-ALIAS>>'),
					array($serviceClass, $serviceMethod, $internalServiceAlias, $serviceAlias),
					$this->serviceMethod);
			}
		}
		
		$src = str_replace('<<SERVICE-METHODS>>', implode(PHP_EOL, $services), $this->serviceProvider);
		file_put_contents($serviceProviderFile, $src);
		
		return $src;
	}
	
	public function writeServiceConfig(){
		$serviceDir = dirname(__DIR__) . '/Service/';
		$serviceConfigFile = __DIR__ . '/../../../config/service.config.services.php';
		
		$serviceFactories = array();
		$serviceInvokables = array();
		
		foreach(scandir($serviceDir) as $k => $v){
			if(is_file($serviceDir . $v) && $v != 'ServiceBase.php'){
		
				$serviceClass = 'EcampCore\Service\\' . substr($v, 0, -4);
				$internalServiceAlias = 'ecamp.internal.service.' . strtolower(substr($v, 0, -11));
				$serviceAlias = 'ecamp.service.' . strtolower(substr($v, 0, -11));
		
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
		
		$src = str_replace(
			array('<<SERVICE-FACTORY>>', '<<SERVICE-INVOKABLES>>'),
			array(implode(PHP_EOL, $serviceFactories), implode(PHP_EOL, $serviceInvokables)),
			$this->serviceConfig);
		file_put_contents($serviceConfigFile, $src);
		
		return $src;
	}
	
}