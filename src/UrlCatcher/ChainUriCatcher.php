<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 22:39
 */
namespace BugCatcher\Reporter\UrlCatcher;

class ChainUriCatcher implements UriCatcherInterface {


	/**
	 * @param UriCatcherInterface[] $uriCatchers
	 */
	public function __construct(private readonly array $uriCatchers) {}

	public function getUri(): string {
		foreach ($this->uriCatchers as $uriCatcher) {
			if ($uriCatcher->isSupported()) {
				return $uriCatcher->getUri();
			}
		}
	}

	public function isSupported(): bool {
		return true;
	}
}