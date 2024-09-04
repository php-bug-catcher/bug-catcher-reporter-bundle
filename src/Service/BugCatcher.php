<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 12. 6. 2024
 * Time: 15:29
 */
namespace BugCatcher\Reporter\Service;

use BugCatcher\Reporter\Event\RecordWriteEvent;
use BugCatcher\Reporter\UrlCatcher\UriCatcherInterface;
use BugCatcher\Reporter\Writer\CollectCodeFrame;
use BugCatcher\Reporter\Writer\WriterInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

class BugCatcher implements BugCatcherInterface {

	public function __construct(
		private readonly WriterInterface     $writer,
		private readonly UriCatcherInterface $uriCatcher,
        private readonly EventDispatcherInterface $eventDispatcher,
		private readonly string              $project,
		private readonly string              $minLevel
	) {}

	public function log(array $data): void {
		if (!array_key_exists("projectCode", $data)) {
			$data["projectCode"] = $this->project;
		}
        $event = $this->eventDispatcher->dispatch(new RecordWriteEvent($data));
        $this->writer->write($event->getData());
	}

	public function logRecord(string $message, int $level, ?string $requestUri = null, array $additional = []): void {
        $data = $additional + [
                "api_uri" => "/api/record_logs",
                "message" => substr($message, 0, 750),
                "level" => $level,
                "projectCode" => $this->project,
                "requestUri" => $requestUri ?? $this->uriCatcher->getUri(),
            ];
        $event = $this->eventDispatcher->dispatch(new RecordWriteEvent($data));
        $this->writer->write($event->getData());
	}

	public function logException(Throwable $throwable, int $level = 500, ?string $requestUri = null): void {

		$data = [
			"api_uri" => "/api/record_log_traces",
			"message" => substr($throwable->getMessage(), 0, 750),
			"level"       => $level,
			"projectCode" => $this->project,
			"requestUri"  => $requestUri??$this->uriCatcher->getUri(),
		];
        $event = $this->eventDispatcher->dispatch(new RecordWriteEvent($data, $throwable));
        $this->writer->write($event->getData());
	}
}