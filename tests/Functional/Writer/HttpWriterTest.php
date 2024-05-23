<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 11:19
 */
namespace BugCatcher\Reporter\Tests\Functional\Writer;

use BugCatcher\Reporter\Writer\HttpWriter;
use Monolog\Level;
use Monolog\LogRecord;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HttpWriterTest extends KernelTestCase {

//	public function testSendLog(): void {
//		$kernel = self::bootKernel();
//		$writer = $kernel->getContainer()->get(HttpWriter::class);
//		$this->assertInstanceOf(HttpWriter::class,$writer);
//
//		$record = new LogRecord(
//			new \DateTimeImmutable(),
//			"default",
//			Level::Critical,
//			"Exception:"
//		);
//		$writer->write($record);
//		$this->assertTrue(true);
//	}
}