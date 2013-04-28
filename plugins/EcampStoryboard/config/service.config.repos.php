<?php
return array(
	
	'aliases' => array(
		'__repos__.ecampStoryboard_SectionRepo' => 'ecampstoryboard.repo.section',
	),

	'factories' => array(
		'ecampstoryboard.repo.section' => new EcampCore\RepositoryUtil\RepositoryFactory('EcampStoryboard\Entity\Section'),
	),
	
);
