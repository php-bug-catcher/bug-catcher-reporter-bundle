<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 21:52
 */
namespace BugCatcher\Reporter\UrlCatcher;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;

class ConsoleUriCatcher implements UriCatcherInterface {

	public function getUri(): string {
		$input = new ArgvInput();

		return $this->getCommandName($input);
	}

	private function getCommandName(InputInterface $input): string {
		/** @var ArgvInput $input */
		$commandName = $input->__toString();
		$fistChar    = substr($commandName, 0, 1);
		if (in_array($fistChar, ["'", '"'])) {
			$commandName = preg_replace("/{$fistChar}/", "", $commandName, 2);
		}

		return $commandName;
	}

	public function isSupported(): bool {
		return 'cli' === PHP_SAPI;
	}
}