<?php

namespace Aspectus\Terminal\Value;

use Aspectus\Terminal\Xterm\ControlDriver\Xterm7BitControlDriver;

class SecondaryDeviceAttributes
{
    public function __construct(
        public readonly string $type,
        public readonly ?string $version = null,        // these nulls here is to accommodate psalm for now
        public readonly ?string $cartridgeRegistration = null
    ) {
    }

    public static function fromString(string $string, string $csi = Xterm7BitControlDriver::CSI): self
    {
        $string = str_replace([$csi . '>', 'c'], '', $string);
        return new self(...explode(';', $string, 3));
    }
}
