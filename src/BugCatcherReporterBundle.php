<?php

namespace BugCatcher\Reporter;

use BugCatcher\Reporter\DependencyInjection\BugCatcherReporterExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html
 */
class BugCatcherReporterBundle extends Bundle
{
	public function getContainerExtension(): ?ExtensionInterface {
		if (null === $this->extension) {
			$this->extension = new BugCatcherReporterExtension();
		}

		return $this->extension;
	}
}