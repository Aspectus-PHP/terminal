<?php

namespace Aspectus\Terminal\Xterm\Event;

use Aspectus\Terminal\Event\InputEvent;

class SpecialKeyEvent extends InputEvent
{
    public const F1 = '<F1>';
    public const F2 = '<F2>';
    public const F3 = '<F3>';
    public const F4 = '<F4>';
    public const F5 = '<F5>';
    public const F6 = '<F6>';
    public const F7 = '<F7>';
    public const F8 = '<F8>';
    public const F9 = '<F9>';
    public const F10 = '<F10>';
    public const F11 = '<F11>';
    public const F12 = '<F12>';

    public const LEFT = '<LEFT>';
    public const RIGHT = '<RIGHT>';
    public const UP = '<UP>';
    public const DOWN = '<DOWN>';

    public function __construct(
        readonly public string $data,
        readonly public string $originalData
    ) {
    }

    public static function create(string $abstractedKey, string $originalData)
    {
        return new self($abstractedKey, $originalData);
    }
}