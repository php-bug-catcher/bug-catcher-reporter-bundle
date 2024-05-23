<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 11:19
 */
namespace BugCatcher\Reporter\Tests\Functional\Writer;

use BugCatcher\Reporter\Tests\Functional\KernelTestCase;
use BugCatcher\Reporter\Writer\HttpWriter;
use DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;

class HttpWriterTest extends KernelTestCase {

	public function testSendLog(): void {
		$kernel = self::bootKernel([
			"http_client" => 'bug_catcher.client',
		]);
		$writer = $kernel->getContainer()->get('bug_catcher.writer.http_writer');
		$this->assertInstanceOf(HttpWriter::class, $writer);

		$record = new LogRecord(
			new DateTimeImmutable(),
			"default",
			Level::Critical,
			"Exception:"
		);
		$writer->write($record);
		$this->assertTrue(true);
	}
}