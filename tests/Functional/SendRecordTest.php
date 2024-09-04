<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 4. 9. 2024
 * Time: 14:26
 */

namespace BugCatcher\Reporter\Tests\Functional;


use BugCatcher\Reporter\Service\BugCatcherInterface;
use BugCatcher\Reporter\Tests\App\KernelTestCase;
use BugCatcher\Reporter\Writer\HttpWriter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class SendRecordTest extends KernelTestCase
{
    public function testBugCatcher()
    {
        $responses = [
            new MockResponse([]),
        ];

        $client = new MockHttpClient($responses);
        $this->getContainer()->set(HttpWriter::class, new HttpWriter($client));
        /** @var BugCatcherInterface $bugCatcher */
        $bugCatcher = $this->getContainer()->get(BugCatcherInterface::class);
        $bugCatcher->log([
            "api_uri" => "/api/record_logs",
            "message" => "My message",
            "level" => 500,
            "projectCode" => "dev",
            "requestUri" => "my uri",
        ]);
        $this->assertTrue(true);
    }
}