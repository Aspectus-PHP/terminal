<?php

namespace Aspectus\Terminal\Xterm\ControlDriver;

class Xterm7BitControlDriver
{
    public const ESC = "\x1b";
    public const IND = self::ESC . 'D';
    public const NEL = self::ESC . 'E';
    public const HTS = self::ESC . 'H';
    public const RI = self::ESC . 'M';
    public const SS2 = self::ESC . 'N';
    public const SS3 = self::ESC . 'O';
    public const DCS = self::ESC . 'P';
    public const SPA = self::ESC . 'V';
    public const EPA = self::ESC . 'W';
    public const SOS = self::ESC . 'X';
    public const DECID = self::ESC . 'Z';
    public const CSI = self::ESC . '[';
    public const ST = self::ESC . '\\';
    public const OSC = self::ESC . ']';
    public const PM = self::ESC . '^';
    public const APC = self::ESC . '_';
}