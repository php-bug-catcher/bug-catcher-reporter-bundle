<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 12:59
 */
namespace BugCatcher\Reporter\Service;

use BugCatcher\Reporter\UrlCatcher\UriCatcherInterface;
use BugCatcher\Reporter\Writer\CollectCodeFrame;
use BugCatcher\Reporter\Writer\WriterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class BugCatcherMonologHandler extends AbstractProcessingHandler {
	use CollectCodeFrame;


	public function __construct(
		private readonly BugCatcherInterface $bugCatcher,
		private readonly bool                $stackTrace,
		private readonly string              $minLevel
	) {
		parent::__construct();
		$this->formatter = new ErrorFormater();
		$this->formatter->includeStacktraces();
	}

	protected function write(array $record): void {
		if ($record['level'] < $this->minLevel) {
			return;
		}
		$stackTrace = null;
		if ($this->stackTrace) {
			$stackTrace = $this->collectFrames($record['formatted']);
		}
		$message             = strtr($record['message'], array_reduce(array_keys($record['context']), function ($acc, $key) use ($record) {
			$acc["{{$key}}"] = $record['context'][$key];

			return $acc;
		}, []));
		$data = [];
		if ($stackTrace) {
			$data['stackTrace'] = $stackTrace;
			$data['api_uri'] = "/api/record_log_traces";
		}
		$this->bugCatcher->logRecord($message, $record['level'], null, $data);
	}
}