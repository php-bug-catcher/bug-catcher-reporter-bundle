<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 3. 9. 2024
 * Time: 16:08
 */

namespace BugCatcher\Reporter\Event;

use BugCatcher\Reporter\Writer\CollectCodeFrame;

class WriteStackTraceListener
{
    use CollectCodeFrame;


    public function __construct(
        private readonly bool $stackTrace,
    ) {
    }

    public function __invoke(RecordWriteEvent $event)
    {
        $data = $event->getData();
        if ($event->throwable) {
            if ($this->stackTrace) {
                $stackTrace = $this->collectFrames($event->throwable->getTraceAsString());
                if ($stackTrace) {
                    $data['stackTrace'] = $stackTrace;
                }
            }
        }
        $event->setData($data);
    }


}