<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 23:05
 */
namespace BugCatcher\Reporter\Tests\Functional\Writer;

use BugCatcher\Reporter\Entity\Project;
use BugCatcher\Reporter\Tests\Functional\KernelTestCase;
use BugCatcher\Reporter\Writer\DoctrineWriter;
use BugCatcher\Reporter\Writer\HttpWriter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Level;
use Monolog\LogRecord;

class DoctrineWriterTest extends KernelTestCase {

	public function testSendLog(): void {
		$kernel = self::bootKernel([
			"connection" => 'default',
		]);
		/** @var EntityManagerInterface $em */
		$em      = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
		$project = new Project();
		$project->setCode("dev");
		$em->persist($project);
		$em->flush();


		$writer = $kernel->getContainer()->get('bug_catcher.writer.doctrine_writer');
		$this->assertInstanceOf(DoctrineWriter::class, $writer);

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