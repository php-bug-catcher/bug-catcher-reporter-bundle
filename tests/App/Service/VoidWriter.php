<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 4. 9. 2024
 * Time: 17:43
 */

namespace BugCatcher\Reporter\Tests\App\Service;

use BugCatcher\Reporter\Writer\WriterInterface;

class VoidWriter implements WriterInterface
{

    private array $data = [];

    function write(array $data): void
    {
        $this->data[] = $data;
    }

    public function popLastRequest(): array
    {
        return array_pop($this->data);
    }
}