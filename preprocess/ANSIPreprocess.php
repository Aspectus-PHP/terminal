<?php

namespace Aspectus\Terminal;

class ANSIPreprocess
{
    public const INDEX = self::ESC . 'D';       // x84  IND
    public const NEXTLINE = self::ESC . 'E';    // x85  NEL
    public const TABSET = self::ESC . 'H';      // x88  HTS
    public const RINDEX = self::ESC . 'M';      // x8d  RI
    public const SINGLE_SHIFT_G2 = self::ESC . 'N'; // x8e  SS2
    public const SINGLE_SHIFT_G3 = self::ESC . 'O'; // x8f  SS3
    public const DCS = self::ESC . 'P';         // x90      Device Control String
    public const START_GUARDED_AREA = self::ESC . 'V';         // x96       SPA
    public const END_GUARDED_AREA = self::ESC . 'W';           // x97       EPA
    public const START_OF_STRING = self::ESC . 'X';         // x98 SOS
    public const RET_TERMINAL_ID = self::ESC . 'Z';         // DECID x9a Obsolete form of CSI c (DA)
    /**
     * CSI (CONTROL_SEQUENCE_INTRODUCER)
     * x9b
     */
    public const CSI = self::ESC . '[';
    public const STRING_TERMINATOR = self::ESC . '\\';         // x9c ST
    public const OSC = self::ESC . ']';         // x9d Operating System Command
    public const PRIVACY_MESSAGE = self::ESC . 'P';         // x9e PM
    public const APC = self::ESC . '_';                 // x9f Application Program Command

    // VT100 Mode
    public const BELL = '\x07';
    public const BACKSPACE = '\x08';
    public const TAB = '\x09';
    public const LF = '\x0a';
    public const CR = '\x0d';
    public const ESC = '\x1b';
    public const SP = ' ';

    public const S7C1T = self::ESC . self::SP . 'F';        // 7 bit controls
    public const S8C1T = self::ESC . self::SP . 'G';        // 8 bit controls

    public const ANSI_LEVEL_1 = self::ESC . self::SP . 'L';
    public const ANSI_LEVEL_2 = self::ESC . self::SP . 'M';
    public const ANSI_LEVEL_3 = self::ESC . self::SP . 'N';

    // these do not seem to be emulated in iterm/kitty but they work on terminal
    public const DOUBLE_HEIGHT_TOP_HALF_LINE = self::ESC . '#3';     // DECDHL
    public const DOUBLE_HEIGHT_BOTTOM_HALF_LINE = self::ESC . '#4';     // DECDHL
    public const SINGLE_WIDTH_LINE = self::ESC . '#5';      // DECSWL
    public const DOUBLE_WIDTH_LINE = self::ESC . '#6';      // DECDWL

    public const SCREEN_ALIGNMENT_TEST = self::ESC . '#8';  // DECALN


    public const SELECT_DEFAULT_CHARSET = self::ESC . '%@'; // ISO 8859-1 (ISO 2022)
    public const SELECT_UTF8_CHARSET = self::ESC . '%G';    // (ISO 2022)

    // Designate Charset

    // ESC ( C
    public const DESIGNATE_G0_CHARSET = self::ESC . '(';
    // ESC ) C
    public const DESIGNATE_G1_CHARSET = self::ESC . ')';
    // ESC * C
    public const DESIGNATE_G2_CHARSET = self::ESC . '*';
    // ESC + C
    public const DESIGNATE_G3_CHARSET = self::ESC . '+';
    /*
    Final character C for designating character sets (0 , A and B apply to VT100 and up, the remainder to VT220 and up):
C = 0 → DEC Special Character and Line Drawing Set
C = A → United Kingdom (UK)
C = B → United States (USASCII)
C = 4 → Dutch
C = C or 5 → Finnish
C = R → French
C = Q → French Canadian
C = K → German
C = Y → Italian
C = E or 6 → Norwegian/Danish
C = Z → Spanish
C = H or 7 → Swedish
C = = → Swiss
        */

    /**
     * DECSC
     */
    public const SAVE_CURSOR_POSITION = self::ESC . '7';

    /**
     * DECRC
     */
    public const RESTORE_CURSOR_POSITION = self::ESC . '8';

    /**
     * DECPAM
     */
    public const SET_APP_KEYPAD = "\e=";

    /**
     * DECPNM
     * Set normal keypad
     */
    public const SET_NUMPAD = "\e>";

    /**
     * If enabled by the hpLowerLeftBugCompat resource
     */
    public const MOVE_CURSOR_TO_LOWER_LEFT = self::ESC . 'F';
    /**
     * RIS
     * Full reset
     */
    public const RESET = "\ec";

    /**
     * Per HP terminals
     * Locks memory above the cursor
     */
    public const MEMORY_LOCK = self::ESC . 'l';

    /**
     * Per HP terminals
     */
    public const MEMORY_UNLOCK = self::ESC . 'm';

    // Invoke Charsets
    public const INVOKE_G2_GL = self::ESC . 'n';    // LS2
    public const INVOKE_G3_GL = self::ESC . 'o';    // LS3
    public const INVOKE_G3_GR = self::ESC . '|';    // LS3R
    public const INVOKE_G2_GR = self::ESC . '}';    // LS2R
    public const INVOKE_G1_GR = self::ESC . '~';    // LS1R

    // APC      [missing]

    // DCS      [missing]


    /** ED */
    public const ERASE_FROM_CURSOR_TO_END = self::CSI . '0J';
    public const ERASE_FROM_START_TO_CURSOR = self::CSI . '1J';
    public const ERASE_DISPLAY = self::CSI . '2J';

    /** EL */
    public const ERASE_FROM_CURSOR_TO_EOL = self::CSI . '0K';
    public const ERASE_FROM_LINE_START_TO_CURSOR = self::CSI . '1K';
    public const ERASE_LINE = self::CSI . '2K';

    // @see https://www.xfree86.org/current/ctlseqs.html#The%20Alternate%20Screen%20Buffer
    public const ENTER_ALTERNATE_SCREEN_BUFFER = self::CSI . "?1047h";
    public const SAVE_CURSOR_AND_ENTER_ALTERNATE_SCREEN_BUFFER = self::CSI . "?1049h";
    public const ENTER_NORMAL_SCREEN_BUFFER = self::CSI . "?1047l";
    public const RESTORE_CURSOR_AND_ENTER_NORMAL_SCREEN_BUFFER = self::CSI . "?1049l";

    public const SHOW_CURSOR = self::CSI . '?25h';
    public const HIDE_CURSOR = self::CSI . '?25l';

    // @see https://espterm.github.io/docs/VT100%20escape%20codes.html
    public const MOVE_CURSOR_TO_UPPER_LEFT = self::CSI . 'H';

    public const ALTERNATE_CHARSET_ON = "\e(0";
    public const ALTERNATE_CHARSET_OFF = "\e(B";

}
