<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 12. 6. 2024
 * Time: 15:29
 */
namespace BugCatcher\Reporter\Service;

use BugCatcher\Reporter\UrlCatcher\UriCatcherInterface;
use BugCatcher\Reporter\Writer\CollectCodeFrame;
use BugCatcher\Reporter\Writer\WriterInterface;

class BugCatcher implements BugCatcherInterface {
	use CollectCodeFrame;

	public function __construct(
		private readonly WriterInterface     $writer,
		private readonly UriCatcherInterface $uriCatcher,
		private readonly string              $project,
		private readonly bool                $stackTrace,
		private readonly string              $minLevel
	) {}

	public function log(array $data): void {
		if (!array_key_exists("projectCode", $data)) {
			$data["projectCode"] = $this->project;
		}
		$this->writer->write($data);
	}

	public function logRecord(string $message, int $level, ?string $requestUri = null, array $additional = []): void {
		$this->writer->write([
				"api_uri" => "/api/record_logs",
				"message" => substr($message, 0, 750),
				"level"       => $level,
				"projectCode" => $this->project,
				"requestUri"  => $requestUri??$this->uriCatcher->getUri(),
			] + $additional);
	}

	public function logException(\Throwable $throwable, int $level = 500, ?string $requestUri = null): void {
		$stackTrace = null;
		if ($this->stackTrace) {
			$stackTrace = $this->collectFrames($throwable->getTraceAsString());
		}
		$data = [
			"api_uri" => "/api/record_log_traces",
			"message" => substr($throwable->getMessage(), 0, 750),
			"level"       => $level,
			"projectCode" => $this->project,
			"requestUri"  => $requestUri??$this->uriCatcher->getUri(),
		];
		if ($stackTrace) {
			$data['stackTrace'] = $stackTrace;
		}
		$this->writer->write($data);
	}
}