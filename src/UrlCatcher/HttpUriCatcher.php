<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 21:46
 */
namespace BugCatcher\Reporter\UrlCatcher;

use Symfony\Component\HttpFoundation\RequestStack;

class HttpUriCatcher implements UriCatcherInterface {

	public function __construct(
		private readonly RequestStack $requestStack
	) {}

	public function getUri(): string {
		$request = $this->requestStack->getMainRequest();
		if (!$request) {
			return "";
		}

		return $request->getSchemeAndHttpHost() . $request->getRequestUri()??"";
	}
}