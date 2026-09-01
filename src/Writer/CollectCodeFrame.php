<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 24. 5. 2024
 * Time: 18:58
 */
namespace BugCatcher\Reporter\Writer;

use Kregel\ExceptionProbe\Stacktrace;
use Throwable;

trait CollectCodeFrame {

	public function collectFrames(?string $stackTrace): ?string {
		if ($stackTrace === null) {
			return null;
		}
		$stacktrace = (new Stacktrace())->parse($stackTrace);

		return serialize($stacktrace);
	}

	/**
	 * Preferred over collectFrames() whenever the Throwable itself is available: the throw site is
	 * only reachable through getFile()/getLine() and would otherwise be missing from the frames.
	 */
	public function collectThrowableFrames(Throwable $throwable): string {
		return serialize((new ThrowSiteStacktrace())->parseThrowable($throwable));
	}
}