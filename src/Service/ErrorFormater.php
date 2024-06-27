<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 28. 5. 2024
 * Time: 20:21
 */
namespace BugCatcher\Reporter\Service;

use Monolog\Formatter\LineFormatter;
use Monolog\Utils;
use Throwable;

class ErrorFormater extends LineFormatter {

	protected function normalizeException($e): string {
		$stackTrace = $e->getTraceAsString();
		$stackTrace = "#0 " . $e->getFile() . "(" . $e->getLine() . "): " . $e->getMessage() . "\n" . $stackTrace;

		return $stackTrace;
	}
}