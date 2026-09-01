<?php

namespace BugCatcher\Reporter\Tests\Functional;

use BugCatcher\Reporter\Service\BugCatcherInterface;
use BugCatcher\Reporter\Service\BugCatcherMonologHandler;
use BugCatcher\Reporter\Tests\App\KernelTestCase;
use BugCatcher\Reporter\Tests\App\Service\VoidWriter;
use Kregel\ExceptionProbe\Codeframe;
use LogicException;
use Monolog\Level;
use Monolog\LogRecord;
use RuntimeException;
use Throwable;

class StackTraceTest extends KernelTestCase
{
    public function testFirstFrameIsTheThrowSite()
    {
        $throwable = $this->throwAndCatch(function () {
            throw new RuntimeException('boom');
        });

        $frames = $this->popFrames(fn(BugCatcherInterface $b) => $b->logException($throwable));

        $this->assertFirstFrameMatches($throwable, $frames);
    }

    /**
     * The throw site used to be appended as a `#0 file(line): message` text line and re-parsed with
     * a greedy regex, which bound to the last `(digits): ` on the line — so a message containing one
     * (`SQLSTATE (1064): ...`) swallowed the real file/line into the file name.
     */
    public function testThrowSiteSurvivesMessageContainingParenthesisedNumber()
    {
        $throwable = $this->throwAndCatch(function () {
            throw new RuntimeException('SQLSTATE (1064): syntax error near FROM');
        });

        $frames = $this->popFrames(fn(BugCatcherInterface $b) => $b->logException($throwable));

        $this->assertFirstFrameMatches($throwable, $frames);
    }

    public function testPreviousExceptionsAreAppended()
    {
        $inner = $this->throwAndCatch(function () {
            throw new LogicException('inner');
        });
        $outer = $this->throwAndCatch(function () use ($inner) {
            throw new RuntimeException('outer', 0, $inner);
        });

        $frames = $this->popFrames(fn(BugCatcherInterface $b) => $b->logException($outer));

        $this->assertFirstFrameMatches($outer, $frames);

        $causedBy = array_values(array_filter(
            $frames,
            fn(Codeframe $frame) => str_starts_with($frame->frame, 'Caused by: ')
        ));
        $this->assertCount(1, $causedBy);
        $this->assertSame($inner->getFile(), $causedBy[0]->file);
        $this->assertSame($inner->getLine(), $causedBy[0]->line);
        $this->assertSame('Caused by: ' . LogicException::class . ': inner', $causedBy[0]->frame);
    }

    public function testMonologHandlerUsesTheThrowableFromContext()
    {
        $throwable = $this->throwAndCatch(function () {
            throw new RuntimeException('boom');
        });

        $frames = $this->popFrames(function () use ($throwable) {
            /** @var BugCatcherMonologHandler $handler */
            $handler = $this->getContainer()->get('bug_catcher.handler');
            $handler->handle(new LogRecord(
                new \DateTimeImmutable(),
                'app',
                Level::Critical,
                'boom',
                ['exception' => $throwable],
            ));
        });

        $this->assertFirstFrameMatches($throwable, $frames);
        // the old path synthesized a "#0 ..." line on top of the trace's own "#0", duplicating it
        $this->assertSame(
            1,
            count(array_filter($frames, fn(Codeframe $frame) => $frame->file === $throwable->getFile()
                && $frame->line === $throwable->getLine()))
        );
    }

    /**
     * @return Codeframe[]
     */
    private function popFrames(callable $report): array
    {
        $report($this->getContainer()->get(BugCatcherInterface::class));

        /** @var VoidWriter $writer */
        $writer = $this->getContainer()->get('test.writer');
        $request = $writer->popLastRequest();

        $this->assertArrayHasKey('stackTrace', $request);
        $frames = unserialize($request['stackTrace']);
        $this->assertContainsOnlyInstancesOf(Codeframe::class, $frames);

        return $frames;
    }

    /**
     * @param Codeframe[] $frames
     */
    private function assertFirstFrameMatches(Throwable $throwable, array $frames): void
    {
        $this->assertNotEmpty($frames);
        $first = $frames[0];

        $this->assertSame($throwable->getFile(), $first->file);
        $this->assertSame($throwable->getLine(), $first->line);
        $this->assertSame($throwable::class . ': ' . $throwable->getMessage(), $first->frame);
        $this->assertArrayHasKey($throwable->getLine(), $first->code);
    }

    private function throwAndCatch(callable $thrower): Throwable
    {
        try {
            $thrower();
        } catch (Throwable $throwable) {
            return $throwable;
        }

        $this->fail('Expected the callable to throw.');
    }
}
