<?php

namespace Aspectus\Terminal\Event;

use Aspectus\Terminal\TerminalDevice;

class InputEvent implements \Stringable
{
    /**
     * The device instance that dispatched this event
     *
     * @var TerminalDevice
     */
    public TerminalDevice $device;

    protected function __construct(
        public readonly string $data
    ) {
    }

    public static function fromString(string $data): self
    {
        return new static($data);
    }

    public function __toString(): string
    {
        return $this->data;
    }
}
