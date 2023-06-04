<?php

use Aspectus\Terminal\Event\InputEvent;
use Aspectus\Terminal\TerminalDevice;
use Revolt\EventLoop;

require \dirname(__DIR__) . '/vendor/autoload.php';

class MathProblem
{
    private string $inputSoFar = '';

    private bool $foundAnswer = false;

    public function __construct(private TerminalDevice $device)
    {
        // we setup the listeners here
        $device->subscribe(InputEvent::class, $this->listenForAnswer(...));
        $device->subscribe(InputEvent::class, $this->listenForQuit(...));

        // and our stty
        exec('stty -echo -icanon min 1 time 0 < /dev/tty');

        // and we send our initial question
        $device->write("How much is 5 times 5 ? (press Q to quit) \n");

        // and we start the event loop
        EventLoop::run();
    }

    public function listenForAnswer(InputEvent $event)
    {
        $this->inputSoFar .= $event->data;

        if ($this->inputSoFar === '25') {
            $this->foundAnswer = true;

            $this->device->write("\nYou got that right!\n");

            // returning true will unregister the listener
            return true;
        }

        if (strlen($this->inputSoFar) >= 2) {
            $this->inputSoFar = '';
            $this->device->write("\nThat is not correct. Try again\n");
        }
    }

    public function listenForQuit(InputEvent $event)
    {
        if ($event->data === 'q') {
            if ($this->foundAnswer) {
                $this->device->write("Well done! Good bye!\n");
            }

            exit();
        }
    }
}

$device = new TerminalDevice();
$mathProblem = new MathProblem($device);
