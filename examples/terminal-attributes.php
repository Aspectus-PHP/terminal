<?php

use Aspectus\Terminal\Event\InputEvent;
use Aspectus\Terminal\Xterm;
use Revolt\EventLoop;

require \dirname(__DIR__) . '/vendor/autoload.php';

// setup stty
exec('stty -echo -icanon min 1 time 0 < /dev/tty');

$xterm = new Xterm();

$primary = $xterm->requestPrimaryDeviceAttributes();
$secondary = $xterm->requestSecondaryDeviceAttributes();

$xterm->subscribe(
    InputEvent::class,
    function () use ($xterm) {
        $xterm
            ->showCursor()
            ->setPrivateModeRestoreCursorAndEnterNormalScreenBuffer()
            ->flush();

        exit();
    }
);


$xterm
    // boot
    ->setPrivateModeSaveCursorAndEnterAlternateScreenBuffer()
    ->hideCursor()
    // set colors
    ->bgBlue()
    ->brightYellow()
    ->eraseAll()
    // Primary DA
    ->moveCursorTo(2, 2)
    ->write("Terminal ID ..... : $primary->terminalId")
    ->moveCursorTo(3, 2)
    ->write("Capabilities .... : " . implode(' ', $primary->capabilities))
    // Secondary DA
    ->moveCursorTo(6, 2)
    ->write("Type ............ : $secondary->type")
    ->moveCursorTo(8, 2)
    ->write("Version ......... : $secondary->version")
    ->moveCursorTo(10, 2)
    ->write("Cartridge Reg. .. : $secondary->cartridgeRegistration")
    //
    ->moveCursorTo(15, 10)
    ->blink()
    ->write("Press any key to exit")
    ->normal()
    ->flush()
;

EventLoop::run();
