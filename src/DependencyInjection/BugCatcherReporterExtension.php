<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 12:31
 */
namespace BugCatcher\Reporter\DependencyInjection;

use BugCatcher\Reporter\Event\WriteStackTraceListener;
use BugCatcher\Reporter\Service\BugCatcherInterface;
use BugCatcher\Reporter\Writer\DoctrineWriter;
use BugCatcher\Reporter\Writer\HttpWriter;
use Exception;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
		} else {
			throw new Exception('You must set writer or http_client');
		}
		$container->setAlias('bug_catcher.writer', $writer);

		$bugCatcher = $container->getDefinition('bug_catcher');
		$bugCatcher->setArgument(0, new Reference('bug_catcher.writer'));
		$bugCatcher->setArgument(1, new Reference($config['uri_cather']));
        $bugCatcher->setArgument(2, new Reference(EventDispatcherInterface::class));
        $bugCatcher->setArgument(3, $config['project']);
		$bugCatcher->setArgument(4, $config['min_level']);



		$monologHandler = $container->getDefinition('bug_catcher.handler');
		$monologHandler->setArgument(0, new Reference(BugCatcherInterface::class));
		$monologHandler->setArgument(1, $config['stack_trace']);
		$monologHandler->setArgument(2, $config['min_level']);
        $monologHandler = $container->getDefinition(WriteStackTraceListener::class);
        $monologHandler->setArgument(0, $config['stack_trace']);

		if (!class_exists(RequestStack::class)) {
			$container->removeDefinition('bug_catcher.url_catcher.console_uri_catcher');
		}

	}

	public function getAlias(): string {
		return 'bug_catcher';
	}


}