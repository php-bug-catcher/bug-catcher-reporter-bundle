<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 12:59
 */
namespace BugCatcher\Reporter\Service;

use BugCatcher\Reporter\Writer\WriterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class BugCatcherHandler extends AbstractProcessingHandler {


	public function __construct(private readonly WriterInterface $writer) {
		parent::__construct();
		$this->formatter = new ErrorFormater(includeStacktraces: true);
	}

	protected function write(LogRecord $record): void {
		$this->writer->write($record);
	}
}