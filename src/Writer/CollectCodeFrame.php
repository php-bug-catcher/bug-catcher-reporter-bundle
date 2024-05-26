<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 24. 5. 2024
 * Time: 18:58
 */
namespace BugCatcher\Reporter\Writer;

use Kregel\ExceptionProbe\Stacktrace;

trait CollectCodeFrame {

	public function collectFrames(?string $stackTrace): ?string {
		if ($stackTrace === null) {
			return null;
		}
		$stacktrace = (new Stacktrace())->parse($stackTrace);

		return serialize($stacktrace);
	}
}