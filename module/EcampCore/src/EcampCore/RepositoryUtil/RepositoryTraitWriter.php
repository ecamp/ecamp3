<?php

namespace EcampCore\RepositoryUtil;

use Zend\Config\Config;

class RepositoryTraitWriter
	extends WriterBase
{

	public function writeRepositoryTraits(){
	
		$config = new Config($this->serviceLocator->get('config'));
	
		foreach($config->ecamp->modules as $module){
			$this->writeRepositoryTrait(
				$module->repos->module_namespace,
				$module->repos->config_file,
				$module->repos->traits_namespace,
				$module->repos->traits_path
			);
		}
	
	}
	
	private function writeRepositoryTrait(
		$moduleNamespace,
		$repositoryConfigFile,
		$traitNamespace,
		$traitPath
	){
		$repositoryFactories = array();
		$repositoryAliases = array();
		
		$repositoryies = $this->getRepositories($moduleNamespace);
		
		foreach($repositoryies as $repository){

			$src = str_replace(
				array(
					"/*TRAIT-NAMESPACE*/", 
					"/*TRAIT-CLASS*/", 
					"/*REPOSITORY-CLASS*/",
					"/*REPOSITORY-PROPERTY*/", 
					"/*GETTER-METHOD*/", 
					"/*SETTER-METHOD*/"
				),
				array(
					$traitNamespace, 
					$this->getRepositoryTraitClass($repository),
					$this->getRepositoryClass($repository),
					$this->getRepositoryProperty($repository),
					$this->getGetterMethod($repository),
					$this->getSetterMethod($repository)
				),
				file_get_contents(__DIR__ . '/tpl/RepositoryTrait.tpl')
			);
			
			$path = $traitPath . $this->getRepositoryTraitClass($repository) . '.php';

			file_put_contents($path, $src);
		}
	}
}
