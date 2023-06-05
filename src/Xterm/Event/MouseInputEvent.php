<?php

namespace Aspectus\Terminal\Xterm\Event;

use Aspectus\Terminal\Event\EscapeSequenceEvent;

class MouseInputEvent extends EscapeSequenceEvent
{
    private const X10 = 0;
    private const NORMAL = 1;
    private const SGR = 2;

    protected function __construct(
        string $sequence,
        public readonly int $x,
        public readonly int $y,
        private readonly int $mode,
        private readonly int $button,
    ) {
        parent::__construct($sequence);
    }

    public static function fromSGR(string $sequence): self
    {
        strtok($sequence, '<');     // discard

        $button = (int) strtok(';');
        $x = (int) strtok(';');
        $last = strtok(';');
        $y = (int) substr($last, 0, -1);

        return new self(
            $sequence,
            $x,
            $y,
            self::SGR,
            $button,
        );
    }

    public static function fromNormal(string $sequence): self
    {
        $startAt = (int) strpos($sequence, "M");
        $bytes = substr($sequence, $startAt + 1);

        $button = ord($bytes[0] ?? '');
        $x = ord($bytes[1] ?? '') - 32;
        $y = ord($bytes[2] ?? '') - 32;

        return new self(
            $sequence,
            $x,
            $y,
            self::NORMAL,
            $button
        );
    }

    public static function fromX10(string $sequence): self
    {
        strtok($sequence, 'M');     // discard

        $button = (int) strtok(';');
        $x = (int) strtok(';');
        $y = (int) strtok(';');

        return new self(
            $sequence,
            $x,
            $y,
            self::X10,
            $button
        );
    }

    public function button1(): bool
    {
        return match ($this->mode) {
            self::X10, self::NORMAL, self::SGR => 0 === ($this->button & 0b11) && !$this->wheelDown(),
            default => false
        };
    }

    public function button2(): bool
    {
        return match ($this->mode) {
            self::X10, self::NORMAL, self::SGR => ($this->button & 0b01) && (0 === ($this->button & 0b100000)),
            default => false
        };

    }
    public function button3(): bool
    {
        // with motion button 3 will be 66
        return match ($this->mode) {
            self::X10, self::NORMAL, self::SGR => ($this->button & 0b10) && !($this->button & 0b01),
            default => false
        };
    }

    public function released(): bool
    {
        return match ($this->mode) {
            self::NORMAL => 3 == ($this->button & 0b11),
            self::SGR => str_ends_with($this->data, 'm'),
            default => false
        };
    }

    public function button4(): bool
    {
        return (bool) match ($this->mode) {
            self::SGR => $this->button & 0b1000,        // this does not seem correct
            default => false
        };

    }
    public function button5(): bool
    {
        return (bool) match ($this->mode) {
            self::SGR => $this->button & 0b1010,        // this does not seem correct
            default => false
        };
    }

    public function button6(): bool
    {
        return match ($this->mode) {
            default => false
        };
    }

    public function button7(): bool
    {
        return match ($this->mode) {
            default => false
        };
    }

    public function button8(): bool
    {
        return (bool) match ($this->mode) {
            self::SGR => $this->button & 0b10000000,
            default => false
        };
    }

    public function button9(): bool
    {
        return match ($this->mode) {
            default => false
        };
    }

    public function button10(): bool
    {
        return match ($this->mode) {
            default => false
        };
    }

    public function button11(): bool
    {
        return match ($this->mode) {
            default => false
        };
    }

    public function wheelUp(): bool
    {
        return match ($this->mode) {
            self::NORMAL => $this->button === 0b1100001,
            self::SGR => $this->button === 0b1000001,
            default => false
        };
    }

    public function wheelDown(): bool
    {
        return match ($this->mode) {
            self::NORMAL => $this->button === 0b1100000,
            self::SGR => $this->button === 0b1000000,
            default => false
        };
    }

    public function shift(): bool
    {
        return (bool) match ($this->mode) {
            self::NORMAL, self::SGR => $this->button & 0b100,
            default => false
        };
    }

    public function alt(): bool
    {
        return (bool) match ($this->mode) {
            self::NORMAL, self::SGR => $this->button & 0b1000,      // SGR (same as Button 4 press?)
            default => false
        };
    }

    public function meta(): bool
    {
        return $this->alt();
    }

    public function ctrl(): bool
    {
        return (bool) match ($this->mode) {
            self::NORMAL, self::SGR => $this->button & 0b10000,
            default => false
        };
    }

    public function motion(): bool
    {
        return (bool) match ($this->mode) {
            self::NORMAL => $this->button & 0b1000000,    // motion will always start from 64
            self::SGR => $this->button & 0b100000,        // here it seems to be just the 32
            default => false
        };
    }
}