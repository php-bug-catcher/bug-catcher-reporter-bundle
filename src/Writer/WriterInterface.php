<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 10:01
 */
namespace BugCatcher\Reporter\Writer;

use Monolog\LogRecord;

interface WriterInterface {
	function write(array $data): void;
}