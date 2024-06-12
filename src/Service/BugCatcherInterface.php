<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 12. 6. 2024
 * Time: 15:24
 */
namespace BugCatcher\Reporter\Service;

interface BugCatcherInterface {
	public function log(array $data): void;

	public function logRecord(string $message, int $level, ?string $requestUri = null, array $additional = []): void;

	public function logException(\Throwable $throwable, int $level = 500, ?string $requestUri = null): void;
}