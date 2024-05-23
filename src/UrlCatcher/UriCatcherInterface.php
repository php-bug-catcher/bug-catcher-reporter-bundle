<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 21:46
 */
namespace BugCatcher\Reporter\UrlCatcher;

interface UriCatcherInterface {
	public function getUri(): string;
}