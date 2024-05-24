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
use BugCatcher\Reporter\UrlCatcher\UriCatcherInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Monolog\LogRecord as MonologLogRecord;

class DoctrineWriter implements WriterInterface {

	use CollectCodeFrame;

	public function __construct(
		private readonly ManagerRegistry     $registry,
		private readonly UriCatcherInterface $uriCatcher,
		private readonly string              $connection,
		private readonly string              $project,
		private readonly string              $minLevel,
		private readonly bool $stackTrace,
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
			throw new Exception("Project '{$this->project}' not found");
		}
		$stackTrace = null;
		if ($this->stackTrace) {
			$stackTrace = $this->collectFrames($record->formatted);
		}
		$record = new LogRecord(
			$project,
			$record->message,
			$record->level->value,
			$this->uriCatcher->getUri(),
			$stackTrace
		);
		$em->persist($record);
		$em->flush();
	}
}