<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 12:31
 */
namespace BugCatcher\Reporter\DependencyInjection;

use BugCatcher\Reporter\Writer\DoctrineWriter;
use BugCatcher\Reporter\Writer\HttpWriter;
use Exception;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BugCatcherReporterExtension extends Extension {

	public function load(array $configs, ContainerBuilder $container) {
		$loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
		$loader->load('services.xml');

		$configuration = $this->getConfiguration($configs, $container);
		$config        = $this->processConfiguration($configuration, $configs);

		if ($config['writer'] !== null) {
			$writer = $config['writer'];
		} elseif ($config['http_client'] !== null) {
			$writer = 'bug_catcher.writer.http_writer';

			$doctrineWriter = $container->getDefinition('bug_catcher.writer.http_writer');
			$doctrineWriter->setArgument(0, new Reference($config['http_client']));
			$doctrineWriter->setArgument(1, new Reference($config['uri_cather']));
			$doctrineWriter->setArgument(2, $config['project']);
			$doctrineWriter->setArgument(3, $config['min_level']);
			$doctrineWriter->setArgument(4, $config['stack_trace']);
		} else {
			throw new Exception('You must set writer or http_client');
		}
		$container->setAlias('bug_catcher.writer', $writer);

		if (!class_exists(RequestStack::class)) {
			$container->removeDefinition('bug_catcher.url_catcher.console_uri_catcher');
		}

	}

	public function getAlias(): string {
		return 'bug_catcher';
	}


}