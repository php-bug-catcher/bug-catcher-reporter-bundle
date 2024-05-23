<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 9:54
 */
namespace BugCatcher\Reporter\Writer;

use BugCatcher\Reporter\Entity\LogRecord;
use BugCatcher\Reporter\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord as MonologLogRecord;

class DoctrineWriter implements WriterInterface {


	public function __construct(
		private readonly ManagerRegistry $registry,
		private readonly string          $connection,
		private readonly string          $project,
		private readonly string          $minLevel,
	) {}

	function write(MonologLogRecord $record): void {
		if ($record->level->value < $this->minLevel) {
			return;
		}
		$em = $this->registry->getManager($this->connection);
		if (!$em->isOpen()) {
			$this->registry->resetManager($this->connection);
		}
		$em->clear();
		$project = $em->getRepository(Project::class)->findOneBy(['code' => $this->project]);
		if (!$project) {
			throw new Exception("Project from BugCatcher not found");
		}
		$record = new LogRecord($project, $record->message, $record->level->value, $requestedUri);
		$em->persist($record);
		$em->flush();
	}
}