<?php
return array(
	
	'factories' => array(
		'ecamp.repo.camp' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Camp'),
		'ecamp.repo.contributor' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Contributor'),
		'ecamp.repo.day' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Day'),
		'ecamp.repo.eventinstance' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\EventInstance'),
		'ecamp.repo.eventprototype' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\EventPrototype'),
		'ecamp.repo.event' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Event'),
		'ecamp.repo.eventresp' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\EventResp'),
		'ecamp.repo.eventtemplate' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\EventTemplate'),
		'ecamp.repo.group' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Group'),
		'ecamp.repo.login' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Login'),
		'ecamp.repo.medium' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Medium'),
		'ecamp.repo.period' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\Period'),
		'ecamp.repo.plugininstance' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\PluginInstance'),
		'ecamp.repo.pluginprototype' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\PluginPrototype'),
		'ecamp.repo.usergroup' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\UserGroup'),
		'ecamp.repo.userrelationship' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\UserRelationship'),
		'ecamp.repo.user' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampCore\Entity\User'),
	),
	
);