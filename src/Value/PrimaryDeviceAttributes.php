<?php

namespace Aspectus\Terminal\Value;

use Aspectus\Terminal\Xterm\ControlDriver\Xterm7BitControlDriver;

final class PrimaryDeviceAttributes
{
    public const VT100 = 'vt100';

    public const VT220 = 'vt220';

    public const VT320 = 'vt320';

    public const VT420 = 'vt320';

    public const COLUMNS_132 = '132-columns';

    public const PRINTER = 'printer';

    public const REGIS_GRAPHICS = 'regis';

    public const SIXEL_GRAPHICS = 'sixel';

    public const SELECTIVE_ERASE = 'selectiveErase';

    public const USER_DEFINED_KEYS = 'userDefinedKeys';

    public const NATIONAL_CHARSETS = 'nationalCharsets';

    public const TECHNICAL_CHARACTERS = 'technicalCharacters';

    public const LOCATOR_PORT = 'locator';

    public const TERMINAL_STATE_INTERROGATION = 'terminalStateInterrogation';

    public const USER_WINDOWS = 'userWindows';

    public const HORIZONTAL_SCROLLING = 'horizontalScrolling';

    public const ANSI_COLOR = 'ansiColor';  // VT525

    public const ANSI_TEXT_LOCATOR = 'ansiTextLocator'; // DEC Locator mode

    public const RECTANGULAR_EDITING = 'rectangularEditing';

    public const UNDEFINED = 'undefined';

    /** @var string[] */
    private const TERMINAL_ID = [
        '1' => self::VT100,
        '60' => self::VT220,
        '62' => self::VT220,    // kitty, might be more than VT220
        '63' => self::VT320,
        '64' => self::VT420
    ];

    /** @var string[] */
    private const CAPABILITIES = [
        '1' => self::COLUMNS_132,
        '2' => self::PRINTER,
        '3' => self::REGIS_GRAPHICS,
        '4' => self::SIXEL_GRAPHICS,
        '6' => self::SELECTIVE_ERASE,
        '8' => self::USER_DEFINED_KEYS,
        '9' => self::NATIONAL_CHARSETS,
        '15' => self::TECHNICAL_CHARACTERS,
        '16' => self::LOCATOR_PORT,
        '17' => self::TERMINAL_STATE_INTERROGATION,
        '18' => self::USER_WINDOWS,
        '21' => self::HORIZONTAL_SCROLLING,
        '22' => self::ANSI_COLOR,
        '28' => self::RECTANGULAR_EDITING,
        '29' => self::ANSI_TEXT_LOCATOR,
    ];

    public readonly string $terminalId;
    public readonly array $capabilities;

    public function __construct(
        string $terminalId,
        string ...$capabilities
    ) {
        $this->terminalId = self::TERMINAL_ID[(int) $terminalId] ?? self::UNDEFINED;

        $identified = [];
        foreach ($capabilities as $identifier) {
            $identified[] = self::CAPABILITIES[(int) $identifier] ?? self::UNDEFINED;
        }
        $this->capabilities = array_unique($identified);
    }

    public static function fromString(string $string, string $csi = Xterm7BitControlDriver::CSI): self
    {
        $parsed = [];

        $string = str_replace([$csi . '?', 'c'], '', $string);
        $result = strtok($string, ';');
        $parsed[] = $result;
        while (false !== ($result = strtok(';'))) {
            $parsed[] = $result;
        }
        $parsed = array_filter($parsed);

        return new self(...$parsed);
    }
}
