<?php

namespace thgs\Tests\Benchmark;

use Aspectus\Terminal\Event\InputEvent;
use Aspectus\Terminal\Xterm;

class XtermBench
{
    /**
     * @Revs(1)
     * @Iterations(100)
     * @Assert("mode(variant.time.avg) < 4 ms")
     * @Assert("mode(variant.mem.peak) < 1.5 mb")
     * @RetryThreshold(2.10)
     */
    public function benchSetup()
    {
        $xterm = new Xterm();
        $xterm->subscribe(InputEvent::class, function (InputEvent $event) {
            $event->device->write('Got: ' . bin2hex($event->data) . PHP_EOL);
        });

        $xterm->subscribe(InputEvent::class, function (InputEvent $event) {
            if ($event->data == 'd') {
                var_dump(PHP_INT_MAX);
            }
        });

        $xterm->subscribe(InputEvent::class, function (InputEvent $event) {
            if ($event->data == 'q') {
                exit();
            }
        });
    }
}
