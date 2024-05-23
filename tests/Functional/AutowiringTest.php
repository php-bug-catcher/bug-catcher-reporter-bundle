<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 13:43
 */
namespace BugCatcher\Reporter\Tests\Functional;

use Monolog\Handler\AbstractProcessingHandler;

class AutowiringTest extends KernelTestCase {


	public function testServiceWiring(): void {
		$kernel = self::bootKernel();

		$container = $kernel->getContainer();
		$handler   = $container->get("bug_catcher.handler");
		$this->assertInstanceOf(AbstractProcessingHandler::class, $handler);
	}

}
