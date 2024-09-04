<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 4. 9. 2024
 * Time: 18:01
 */

namespace BugCatcher\Reporter\Tests\Functional;

use BugCatcher\Reporter\Service\BugCatcherInterface;
use BugCatcher\Reporter\Tests\App\EventSubscriber\RecordWriteListener;
use BugCatcher\Reporter\Tests\App\KernelTestCase;
use BugCatcher\Reporter\Tests\App\Service\VoidWriter;

class EventListenerTest extends KernelTestCase
{
    public function testBugCatcherLog()
    {

        $listener = $this->getContainer()->get(RecordWriteListener::class);
        $this->assertInstanceOf(RecordWriteListener::class, $listener);
        $listener->use(true);
        /** @var BugCatcherInterface $bugCatcher */
        $bugCatcher = $this->getContainer()->get(BugCatcherInterface::class);
        $data = [
            'projectCode' => 'dev',
            'from_listener' => 'true'
        ];
        $bugCatcher->log($data);
        $writer = $this->getContainer()->get('test.writer');
        $this->assertInstanceOf(VoidWriter::class, $writer);
        $this->assertSame($data, $writer->popLastRequest());
    }
}