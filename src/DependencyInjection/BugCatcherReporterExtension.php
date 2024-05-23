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
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BugCatcherReporterExtension extends Extension {

	public function load(array $configs, ContainerBuilder $container) {
		$loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
		$loader->load('services.xml');

		$configuration = $this->getConfiguration($configs, $container);
		$config        = $this->processConfiguration($configuration, $configs);

		if ($config['api_url'] !== null) {
			$writer = 'bug_catcher.writer.http_writer';
		} else {
			$writer = 'bug_catcher.writer.doctrine_writer';
		}
		if ($config['writer'] !== null) {
			$writer = $config['writer'];
		}
		$container->setAlias('bug_catcher.writer', $writer);

		$config = $this->processConfiguration($configuration, $configs);

		$doctrineWriter = $container->getDefinition('bug_catcher.writer.doctrine_writer');
		$doctrineWriter->setArgument(1, $config['connection']);
		$doctrineWriter->setArgument(2, $config['project']);
		$doctrineWriter->setArgument(3, $config['min_level']);

		$doctrineWriter = $container->getDefinition('bug_catcher.writer.http_writer');
		$doctrineWriter->setArgument(0, $config['api_url']);
		$doctrineWriter->setArgument(1, $config['project']);
		$doctrineWriter->setArgument(2, $config['min_level']);
	}

	public function getAlias(): string {
		return 'bug_catcher';
	}


}