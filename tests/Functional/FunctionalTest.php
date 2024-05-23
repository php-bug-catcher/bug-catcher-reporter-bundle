<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 13:43
 */
namespace BugCatcher\Reporter\Tests\Functional;

use BugCatcher\Reporter\BugCatcherReporterBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;

class FunctionalTest extends KernelTestCase {


	public function testServiceWiring(): void {
		$kernel = self::bootKernel([
			"project" => "dev",
		]);

		$container = $kernel->getContainer();
		$handler   = $container->get("bug_catcher.handler");
		$this->assertInstanceOf(AbstractProcessingHandler::class, $handler);
	}

	protected static function bootKernel(array $options = []): KernelInterface {
		static::ensureKernelShutdown();

		$kernel = new Kernel($options);
		$kernel->boot();
		static::$kernel = $kernel;
		static::$booted = true;

		return static::$kernel;
	}
}
