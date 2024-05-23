<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 10:15
 */
namespace BugCatcher\Reporter\Writer;

use Monolog\LogRecord;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpWriter implements WriterInterface {


	public function __construct(
		private readonly string     $apiUrl,
		private readonly string     $project,
		private readonly string     $minLevel,
		private HttpClientInterface $client,
	) {}

	function write(LogRecord $record): void {
		if ($record->level->value < $this->minLevel) {
			return;
		}
		$data = [

		];
		$this->client->request("POST", $this->apiUrl, [
			'headers' => [
				'Content-Type' => 'text/plain',
			],
			"body"    => json_encode($data),
		]);
	}
}