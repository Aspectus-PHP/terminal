<?php

namespace Aspectus\Terminal\Xterm\Event;

use Aspectus\Terminal\Event\EscapeSequenceEvent;
use Aspectus\Terminal\Event\EventFactoryInterface;
use Aspectus\Terminal\Event\InputEvent;

class XtermEventFactory implements EventFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createEvent(string $received): ?InputEvent
    {
        return match ($received) {
            // > escape
            // todo: add 8bit if there is one -- not clear yet
            "\x1b" => SpecialKeyEvent::create(SpecialKeyEvent::ESC, $received),

            // others
            "\x0a" => SpecialKeyEvent::create(SpecialKeyEvent::ENTER, $received),
            "\x09" => SpecialKeyEvent::create(SpecialKeyEvent::TAB, $received),
            "\x7f" => SpecialKeyEvent::create(SpecialKeyEvent::BACKSPACE, $received),
            "\x20" => SpecialKeyEvent::create(SpecialKeyEvent::SPACE, $received),

            // > FUNCTION KEYS
            // PC-Style 7bit, PC-Style 8bit, VT52-Style
            "\x1bOP", "\x8fP", "\x1b[11~", "\eP" => SpecialKeyEvent::create(SpecialKeyEvent::F1, $received),
            "\x1bOQ", "\x8fQ", "\x1b[12~", "\eQ" => SpecialKeyEvent::create(SpecialKeyEvent::F2, $received),
            "\x1bOR", "\x8fR", "\x1b[13~", "\eR" => SpecialKeyEvent::create(SpecialKeyEvent::F3, $received),
            "\x1bOS", "\x8fS", "\x1b[14~", "\eS" => SpecialKeyEvent::create(SpecialKeyEvent::F4, $received),
                               "\x1b[15~"        => SpecialKeyEvent::create(SpecialKeyEvent::F5, $received),
                               "\x1b[17~"        => SpecialKeyEvent::create(SpecialKeyEvent::F6, $received),
                               "\x1b[18~"        => SpecialKeyEvent::create(SpecialKeyEvent::F7, $received),
                               "\x1b[19~"        => SpecialKeyEvent::create(SpecialKeyEvent::F8, $received),
                               "\x1b[20~"        => SpecialKeyEvent::create(SpecialKeyEvent::F9, $received),
                               "\x1b[21~"        => SpecialKeyEvent::create(SpecialKeyEvent::F10, $received),
                               "\x1b[23~"        => SpecialKeyEvent::create(SpecialKeyEvent::F11, $received),
                               "\x1b[24~"        => SpecialKeyEvent::create(SpecialKeyEvent::F12, $received),

            // >> NON-FUNCTION keys
            // > arrow keys
            // 7bit DEC/SUN, 8bit DEC/SUN, HP/VT52, 7 bit SCO, 8 bit SCO
            "\x1bOA", "\x8fA", "\eA", "\x1b[A", "\x9bA" => SpecialKeyEvent::create(SpecialKeyEvent::UP, $received),
            "\x1bOB", "\x8fB", "\eB", "\x1b[B", "\x9bB" => SpecialKeyEvent::create(SpecialKeyEvent::DOWN, $received),
            "\x1bOC", "\x8fC", "\eC", "\x1b[C", "\x9bC" => SpecialKeyEvent::create(SpecialKeyEvent::RIGHT, $received),
            "\x1bOD", "\x8fD", "\eD", "\x1b[D", "\x9bD" => SpecialKeyEvent::create(SpecialKeyEvent::LEFT, $received),

            // >> MOUSE FOCUS
            // 7 bit, 8 bit (& repeat)
            "\x1b[I", "\x9bI", "\x1b[O", "\x9bO" => MouseFocusEvent::create($received),

            default => match ($received[0]) {
                // todo: could $received[2] not be set?
                "\x1b" => match ($received[2]) {
                    '<' => MouseInputEvent::fromSGR($received),
                    'M' => MouseInputEvent::fromNormal($received),
                    default => EscapeSequenceEvent::fromString($received)
                },
                "\x9b" => match ($received[1]) {
                    '<' => MouseInputEvent::fromSGR($received),
                    'M' => MouseInputEvent::fromNormal($received),
                    default => EscapeSequenceEvent::fromString($received)
                },
                default => InputEvent::fromString($received),
            }
        };
    }
}