<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 3. 9. 2024
 * Time: 16:00
 */

namespace BugCatcher\Reporter\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

class RecordWriteEvent extends Event
{


    public function __construct(
        private array $data,
        public readonly ?Throwable $throwable = null
    ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }


}