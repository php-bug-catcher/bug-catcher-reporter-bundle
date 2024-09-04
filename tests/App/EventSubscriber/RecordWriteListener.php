<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 3. 9. 2024
 * Time: 16:08
 */

namespace BugCatcher\Reporter\Tests\App\EventSubscriber;


use BugCatcher\Reporter\Event\RecordWriteEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class RecordWriteListener
{

    private bool $use = false;

    public function use(bool $use): void
    {
        $this->use = $use;
    }

    public function __invoke(RecordWriteEvent $event)
    {
        if (!$this->use) {
            return;
        }
        $event->setData(array_merge($event->getData(), ['from_listener' => 'true']));
    }


}