<?php

namespace Aspectus\Terminal\Event;

use Aspectus\Terminal\TerminalDevice;

class InputEvent implements \Stringable
{
    /**
     * The device instance that dispatched this event
     */
    public ?TerminalDevice $device = null;

    protected function __construct(
        readonly public string $data
    ) {
    }

    public static function fromString(string $data): self
    {
        return new self($data);
    }

    public function __toString(): string
    {
        return $this->data;
    }
}
