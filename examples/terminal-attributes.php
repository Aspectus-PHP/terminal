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
    ->flush()
;

EventLoop::repeat(1, function () use ($xterm, $primary, $secondary) {
    $xterm
        // set colors
        ->bgBlue()
        ->brightYellow()
        ->eraseAll();

    $view = [
        // Primary
        'Terminal ID' => $primary->terminalId,
        'Capabilities' => "\n\t" . implode(
            " ",
            // only first letter for when its running on xterm
            array_map(fn ($capability) => substr($capability, 0, 1), $primary->capabilities)
        ),

        // Secondary
        'Type' => $secondary->type,
        'Version' => $secondary->version,
        'Cartridge Reg' => $secondary->cartridgeRegistration,

        // Sizes
        'TextArea Position' => implode(' x ', $xterm->reportTextAreaPosition()),
        'TextArea Size (px)' => implode(' x ', $xterm->reportTextAreaSizePixels()),
        'TextArea Size (char)' => implode(' x ', $xterm->reportTextAreaSizeCharacters()),
        'Window Size (px)' => implode(' x ', $xterm->reportWindowSizePixels()),
        'Screen Size (px)' => implode(' x ', $xterm->reportScreenSizePixels()),
        'Screen Size (char)' => implode(' x ', $xterm->reportScreenSizeCharacters()),
        'Character size (px)' => implode(' x ', $xterm->reportCharacterSizePixels()),
    ];

    $lines = [
        [2,2],
        [3,2],
        [6,2],
        [8,2],
        [10,2],
        [2, 40],
        [3, 40],
        [4, 40],
        [6, 40],
        [8, 40],
        [9, 40],
        [11, 40],
    ];

    $i = 0;
    foreach ($view as $title => $data) {
        $element = str_pad($title . ' ', 25, '.') . ' : ' . $data;
        $xterm
            ->moveCursorTo(...$lines[$i++])
            ->write($element);
    }
        //
    $xterm
        ->moveCursorTo(15, 10)
        ->blink()
        ->write("Press any key to exit or resize/move your screen")
        ->normal()
        ->flush()
    ;
});

EventLoop::run();
