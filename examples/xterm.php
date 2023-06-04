<?php


use Aspectus\Terminal\Xterm;
use function Amp\delay;

require \dirname(__DIR__) . '/vendor/autoload.php';

$xterm = new Xterm();

register_shutdown_function(function () use ($xterm) {
    $xterm
        // return to normal screen buffer
        ->setPrivateModeClearAndUseNormalScreenBuffer()
        // show cursor
        ->showCursor()
        ->flush();
});

$xterm
        // hide cursor
    ->hideCursor()
        // enter alt screen buffer
    ->setPrivateModeAlternateScreenBuffer()
        // set some colors
    ->bgBlue()
    ->brightYellow()
    ->eraseAll()
        // execute
    ->flush();

foreach (range(1,10) as $i) {
    $xterm
        ->moveCursorTo(10, 10)
        ->write("This is iteration #$i")
        ->flush();
    delay(1);
}

$xterm
    // reset to normal colours
    ->normal()
    ->eraseAll()
    ->green()
    ->write("Goodbye!\n")
    ->flush();

delay(1);
