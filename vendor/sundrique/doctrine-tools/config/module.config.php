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

return array(
	'console' => array(
		'router' => array(
			'routes' => array(
				'doctrinetools' => array(
					'type' => 'colon',
					'options' => array(
						'defaults' => array(
							'controller' => 'DoctrineTools\Controller\Index',
							'action' => 'index'
						)
					)
				)
			)
		)
	),
	'controllers' => array(
		'invokables' => array(
			'DoctrineTools\Controller\Index' => 'DoctrineTools\Controller\IndexController'
		)
	),
	'route_manager' => array(
		'invokables' => array(
			'colon' => 'DoctrineTools\Mvc\Router\Console\SymfonyCli',
		),
	),
	'doctrinetools' => array(
		'migrations' => array(
			'directory' => 'data/DoctrineTools/Migrations',
			'namespace' => 'DoctrineTools\Migrations',
			'table' => 'migrations'
		)
	),
	'service_manager' => array(
		'factories' => array(
			'doctrinetools.migrations_configuration' => function ($serviceManager) {
				$connection = $serviceManager->get('doctrine.connection.orm_default');

				$appConfig = $serviceManager->get('Config');
				$migrationsConfig = $appConfig['doctrinetools']['migrations'];

				$configuration = new \Doctrine\DBAL\Migrations\Configuration\Configuration($connection);
				$configuration->setMigrationsDirectory($migrationsConfig['directory']);
				$configuration->setMigrationsNamespace($migrationsConfig['namespace']);
				$configuration->setMigrationsTableName($migrationsConfig['table']);
				$configuration->registerMigrationsFromDirectory($migrationsConfig['directory']);

				return $configuration;
			},
			'doctrinetools.helper_set' => function ($serviceManager) {
				$connection = $serviceManager->get('doctrine.connection.orm_default');

				$entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

				$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
					'dialog' => new \Symfony\Component\Console\Helper\DialogHelper(),
					'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($connection),
					'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
				));

				return $helperSet;
			},
			'doctrinetools.console_application' => function ($serviceManager) {
				$helperSet = $serviceManager->get('doctrinetools.helper_set');

				$cli = new \Symfony\Component\Console\Application('Doctrine Tools', \DoctrineTools\Version::VERSION);
				$cli->setCatchExceptions(true);
				$cli->setAutoExit(false);
				$cli->setHelperSet($helperSet);
				$cli->addCommands(array(
					$serviceManager->get('doctrinetools.dbal.runsql'),
					$serviceManager->get('doctrinetools.dbal.import'),

					$serviceManager->get('doctrinetools.orm.clear-cache.metadata'),
					$serviceManager->get('doctrinetools.orm.clear-cache.result'),
					$serviceManager->get('doctrinetools.orm.clear-cache.query'),
					$serviceManager->get('doctrinetools.orm.schema-tool.create'),
					$serviceManager->get('doctrinetools.orm.schema-tool.update'),
					$serviceManager->get('doctrinetools.orm.schema-tool.drop'),
					$serviceManager->get('doctrinetools.orm.ensure-production-settings'),
					$serviceManager->get('doctrinetools.orm.convert-d1-schema'),
					$serviceManager->get('doctrinetools.orm.generate-repositories'),
					$serviceManager->get('doctrinetools.orm.generate-entities'),
					$serviceManager->get('doctrinetools.orm.generate-proxies'),
					$serviceManager->get('doctrinetools.orm.convert-mapping'),
					$serviceManager->get('doctrinetools.orm.run-dql'),
					$serviceManager->get('doctrinetools.orm.validate-schema'),
					$serviceManager->get('doctrinetools.orm.info'),

					$serviceManager->get('doctrinetools.migrations.execute'),
					$serviceManager->get('doctrinetools.migrations.generate'),
					$serviceManager->get('doctrinetools.migrations.migrate'),
					$serviceManager->get('doctrinetools.migrations.status'),
					$serviceManager->get('doctrinetools.migrations.version'),
					$serviceManager->get('doctrinetools.migrations.diff'),
				));

				return $cli;
			},
			// Migrations commands
			'doctrinetools.migrations.generate' => new \DoctrineTools\Service\MigrationsCommandFactory('generate'),
			'doctrinetools.migrations.execute' => new \DoctrineTools\Service\MigrationsCommandFactory('execute'),
			'doctrinetools.migrations.migrate' => new \DoctrineTools\Service\MigrationsCommandFactory('migrate'),
			'doctrinetools.migrations.status' => new \DoctrineTools\Service\MigrationsCommandFactory('status'),
			'doctrinetools.migrations.version' => new \DoctrineTools\Service\MigrationsCommandFactory('version'),
			'doctrinetools.migrations.diff' => new \DoctrineTools\Service\MigrationsCommandFactory('diff'),
		),
		'invokables' => array(
			// DBAL commands
			'doctrinetools.dbal.runsql' => '\Doctrine\DBAL\Tools\Console\Command\RunSqlCommand',
			'doctrinetools.dbal.import' => '\Doctrine\DBAL\Tools\Console\Command\ImportCommand',
			// ORM Commands
			'doctrinetools.orm.clear-cache.metadata' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand',
			'doctrinetools.orm.clear-cache.result' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand',
			'doctrinetools.orm.clear-cache.query' => '\Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand',
			'doctrinetools.orm.schema-tool.create' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand',
			'doctrinetools.orm.schema-tool.update' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand',
			'doctrinetools.orm.schema-tool.drop' => '\Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand',
			'doctrinetools.orm.ensure-production-settings' => '\Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand',
			'doctrinetools.orm.convert-d1-schema' => '\Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand',
			'doctrinetools.orm.generate-repositories' => '\Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand',
			'doctrinetools.orm.generate-entities' => '\Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand',
			'doctrinetools.orm.generate-proxies' => '\Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand',
			'doctrinetools.orm.convert-mapping' => '\Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand',
			'doctrinetools.orm.run-dql' => '\Doctrine\ORM\Tools\Console\Command\RunDqlCommand',
			'doctrinetools.orm.validate-schema' => '\Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand',
			'doctrinetools.orm.info' => '\Doctrine\ORM\Tools\Console\Command\InfoCommand',
		)
	)
);
