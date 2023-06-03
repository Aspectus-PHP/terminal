<?php

namespace thgs\Tests\Benchmark;

use Aspectus\Terminal\Event\InputEvent;
use Aspectus\Terminal\TerminalDevice;

class TerminalDeviceBench
{
    /**
     * @Revs(1)
     * @Iterations(100)
     * @Assert("mode(variant.time.avg) < 3 ms")
     * @Assert("mode(variant.mem.peak) < 1.3 mb")
     * @RetryThreshold(2.10)
     */
    public function benchSetup()
    {
        $terminal = new TerminalDevice();
        $terminal->subscribe(InputEvent::class, function (InputEvent $event) {
            $event->device->write('Got: ' . bin2hex($event->data) . PHP_EOL);
        });

        $terminal->subscribe(InputEvent::class, function (InputEvent $event) {
            if ($event->data == 'd') {
                var_dump(PHP_INT_MAX);
            }
        });

        $terminal->subscribe(InputEvent::class, function (InputEvent $event) {
            if ($event->data == 'q') {
                exit();
            }
        });
    }
}
