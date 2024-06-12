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
		private readonly WriterInterface     $writer,
		private readonly UriCatcherInterface $uriCatcher,
		private readonly string              $project,
		private readonly bool                $stackTrace,
		private readonly string              $minLevel
	) {
		parent::__construct();
		$this->formatter = new ErrorFormater(includeStacktraces: true);
	}

	protected function write(LogRecord $record): void {
		if ($record->level->value < $this->minLevel) {
			return;
		}
		$stackTrace = null;
		if ($this->stackTrace) {
			$stackTrace = $this->collectFrames($record->formatted);
		}
		$message = strtr($record->message, array_reduce(array_keys($record->context), function ($acc, $key) use ($record) {
			$acc["{{$key}}"] = $record->context[$key];

			return $acc;
		}, []));
		$data    = [
			"message"     => $message,
			"level"       => $record->level->value,
			"projectCode" => $this->project,
			"requestUri"  => $this->uriCatcher->getUri(),
		];
		if ($stackTrace) {
			$data['stackTrace'] = $stackTrace;
		}
		$this->writer->write($data);
	}
}