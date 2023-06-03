<?php

namespace Aspectus\Terminal\Xterm\ControlDriver;

class Xterm8BitControlDriver
{
    public const ESC = "\x1b";
    public const IND = "\x84";
    public const NEL = "\x85";
    public const HTS = "\x88";
    public const RI = "\x8d";
    public const SS2 = "\x8e";
    public const SS3 = "\x8f";
    public const DCS = "\x90";
    public const SPA = "\x96";
    public const EPA = "\x97";
    public const SOS = "\x98";
    public const DECID = "\x9a";
    public const CSI = "\x9b";
    public const ST = "\x9c";
    public const OSC = "\x9d";
    public const PM = "\x9e";
    public const APC = "\x9f";
}