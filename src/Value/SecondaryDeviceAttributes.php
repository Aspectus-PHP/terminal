<?php

namespace Aspectus\Terminal\Value;

use Aspectus\Terminal\Xterm\ControlDriver\Xterm7BitControlDriver;

final class SecondaryDeviceAttributes
{
    public const VT100 = 'vt100';

    public const VT220= 'vt220';

    public const VT240= 'vt240';

    public const VT330 = 'vt330';

    public const VT340 = 'vt340';

    public const VT320 = 'vt320';

    public const VT382 = 'vt382';

    public const VT420 = 'vt420';

    public const VT510 = 'vt510';

    public const VT520 = 'vt520';

    public const VT525 = 'vt525';

    // todo: this is an int key?

    /** @var string[] */
    private const TERMINAL_TYPE = [
        '0'  => self::VT100,
        '1'  => self::VT220,
        '2'  => self::VT240, // or VT241
        '18' => self::VT330,
        '19' => self::VT340,
        '24' => self::VT320,
        '32' => self::VT382,
        '41' => self::VT420,
        '61' => self::VT510,
        '64' => self::VT520,
        '65' => self::VT525,
    ];

    private const UNDEFINED = 'undefined';

    public function __construct(
        public readonly string $type,
        public readonly ?string $version = null,        // these nulls here is to accommodate psalm for now
        public readonly ?string $cartridgeRegistration = null
    ) {
    }

    public static function fromString(string $string, string $csi = Xterm7BitControlDriver::CSI): self
    {
        $string = str_replace([$csi . '>', 'c'], '', $string);

        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        [$type, $version, $cartridgeRegistration] = explode(';', $string, 3);

        return new self(self::TERMINAL_TYPE[(int) $type] ?? self::UNDEFINED, $version, $cartridgeRegistration);
    }
}
