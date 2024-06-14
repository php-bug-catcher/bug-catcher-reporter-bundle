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
	public function __construct(
		private readonly HttpClientInterface $client,
	) {}

	function write(array $data): void {
		if (!array_key_exists("api_uri", $data)) {
			throw new Exception("api_uri not porvided for logging to bugcatcher");
		}
		$path = $data["api_uri"];
		unset($data["api_uri"]);
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