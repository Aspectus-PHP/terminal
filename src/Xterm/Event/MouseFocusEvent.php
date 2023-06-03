<?php

namespace Aspectus\Terminal\Xterm\Event;

use Aspectus\Terminal\Event\EscapeSequenceEvent;

class MouseFocusEvent extends EscapeSequenceEvent
{
    public static function create(string $sequence): self
    {
        return new self($sequence);
    }

    public function focus(): bool
    {
        return str_ends_with($this->data, 'I');
    }
}