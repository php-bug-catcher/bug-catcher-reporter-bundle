<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 10:15
 */
namespace BugCatcher\Reporter\Writer;

use BugCatcher\Reporter\UrlCatcher\UriCatcherInterface;
use Exception;
use Monolog\LogRecord;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpWriter implements WriterInterface {
	use CollectCodeFrame;

	public function __construct(
		private readonly HttpClientInterface $client,
		private readonly UriCatcherInterface $uriCatcher,
		private readonly string              $project,
		private readonly string              $minLevel,
		private readonly bool $stackTrace,
	) {}

	function write(LogRecord $record): void {
		if ($record->level->value < $this->minLevel) {
			return;
		}
		$stackTrace = null;
		if ($this->stackTrace) {
			$stackTrace = $this->collectFrames($record->formatted);
		}
		$message = strtr($record->message, array_reduce(array_keys($record->context), function ($acc, $key) use ($record) {
			$acc["{{$key}}"] = $record->context[$key];

			return $acc;
		}, []));
		$path = '/api/record_logs';
		$data = [
			"message" => $message,
			"level"       => $record->level->value,
			"projectCode" => $this->project,
			"requestUri"  => $this->uriCatcher->getUri(),
		];
		if ($stackTrace) {
			$path = '/api/record_log_traces';
			$data['stackTrace'] = $stackTrace;
		}
		$response = $this->client->request("POST", $path, [
			'headers' => [
				'Content-Type' => 'application/json',
				'accept'       => 'application/json',
			],
			"body"    => json_encode($data),
		]);
		if ($response->getStatusCode() !== 201) {
			throw new Exception("Error during sending log record to BugCatcher.\n" . $response->getContent(false));
		}
	}
}