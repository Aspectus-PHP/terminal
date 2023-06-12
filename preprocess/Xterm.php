<?php

require \dirname(__DIR__) . '/vendor/autoload.php';

require 'ANSIPreprocess.php';

use Aspectus\Terminal\ANSIPreprocess as ANSI;

$methods = [
    // Start arrow at column 33

    // Xterm VT100 Mode (102 + support for VT220, 320, 420, ISO 6429 and aixterm)

    // -- Single character functions (some missing)  -------------------
    'bell'                      => ANSI::BELL,
    'backspace'                 => ANSI::BACKSPACE,
    'cr'                        => ANSI::CR,
    // Return Terminal Status (Ctrl-E). Default response is an empty string, but may be overridden by a resource answerbackString.
    // 'returnTerminalStatus' => ??  ENQ ??
    // Form Feed or New Page (NP) (Ctrl-L) same as LF
    // 'ff => ?? FF ??
    'lf'                        => ANSI::LF,

    // -- Begining with ESC ---------------------------------

    // todo: these could be grouped
    's7c1t'                     => ANSI::S7C1T,
    's8c1t'                     => ANSI::S8C1T,

    // todo: these could be grouped
    'ansiLevel1'                => ANSI::ANSI_LEVEL_1,
    'ansiLevel2'                => ANSI::ANSI_LEVEL_2,
    'ansiLevel3'                => ANSI::ANSI_LEVEL_3,

    // todo: these could be grouped
    'doubleHeightTopHalfLine'   => ANSI::DOUBLE_HEIGHT_TOP_HALF_LINE,
    'doubleHeightBottomHalfLine'
                                => ANSI::DOUBLE_HEIGHT_BOTTOM_HALF_LINE,
    'singleWidthLine'           => ANSI::SINGLE_WIDTH_LINE,
    'doubleWidthLine'           => ANSI::DOUBLE_WIDTH_LINE,

    'screenAlignmentTest'       => ANSI::SCREEN_ALIGNMENT_TEST,

    // todo: these could be grouped
    'selectDefaultCharset'      => ANSI::SELECT_DEFAULT_CHARSET,
    'selectUTF8Charset'         => ANSI::SELECT_UTF8_CHARSET,
    'selectG0Charset'           => ANSI::DESIGNATE_G0_CHARSET,
    'selectG1Charset'           => ANSI::DESIGNATE_G1_CHARSET,
    'selectG2Charset'           => ANSI::DESIGNATE_G2_CHARSET,
    'selectG3Charset'           => ANSI::DESIGNATE_G3_CHARSET,
    // 0, A and B apply to VT100, remainder to VT220 and up
    'selectDECSpecialAndLineDrawingCharset' => ANSI::ESC . '+0',
    'selectUKCharset'           => ANSI::ESC . '+A',
    'selectUSCharset'           => ANSI::ESC . '+B',
    'selectDutchCharset'        => ANSI::ESC . '+4',
    'selectFinnishCharset'      => ANSI::ESC . '+C',     // or '+5'
    'selectFrenchCharset'       => ANSI::ESC . '+R',
    'selectFrenchCanadianCharset'
                                => ANSI::ESC . '+Q',
    'selectGermanCharset'       => ANSI::ESC . '+K',
    'selectItalianCharset'      => ANSI::ESC . '+Y',
    'selectNorwegianCharset'    => ANSI::ESC . '+E',       // same as 'Danish', also with '+6'
    'selectSpanishCharset'      => ANSI::ESC . '+Z',
    'selectSwedishCharset'      => ANSI::ESC . '+H',         // or '+7'
    'selectSwissCharset'        => ANSI::ESC . '+=',

    'saveCursor'                => ANSI::SAVE_CURSOR_POSITION,
    'restoreCursor'             => ANSI::RESTORE_CURSOR_POSITION,

    'setApplicationKeypad'      => ANSI::SET_APP_KEYPAD,
    'setNormalKeypad'           => ANSI::SET_NUMPAD,

    // If enabled by the hpLowerLeftBugCompat resource
    'moveCursorToLowerLeft'     => ANSI::MOVE_CURSOR_TO_LOWER_LEFT,

    'reset'                     => ANSI::RESET,

    'lockMemory'                => ANSI::MEMORY_LOCK,
    'unlockMemory'              => ANSI::MEMORY_UNLOCK,

    'invokeG2AsGL'              => ANSI::INVOKE_G2_GL,
    'invokeG3AsGL'              => ANSI::INVOKE_G3_GL,
    'invokeG3AsGR'              => ANSI::INVOKE_G3_GR,
    'invokeG2AsGR'              => ANSI::INVOKE_G2_GR,
    'invokeG1AsGR'              => ANSI::INVOKE_G1_GR,

    // --- Application Program-Control functions -----------
    // No APC functions ?

    // --- Device Control functions --------------------------
    // Not implemented yet.


    // --- CSI Functions (Control sequence) ---------------------------------

//    'insertBlankCharacters' => '$this->driver::CSI . \'' . ARG . '@\'',
    'insertBlankCharacters'     => ['%CSI%', '$count', '@'],


    // todo: these could be grouped
    'cursorUp'                  => ['%CSI%', '$times', 'A'],
    'cursorDown'                => ['%CSI%', '$times', 'B'],
    'cursorForward'             => ['%CSI%', '$times', 'C'],
    'cursorBackward'            => ['%CSI%', '$times', 'D'],
    'cursorNextLine'            => ['%CSI%', '$times', 'E'],
    'cursorPrecedingLine'       => ['%CSI%', '$times', 'F'],
    'cursorMoveToColumn'        => ['%CSI%', '$column', 'G'],
    'cursorMoveToRow'           => ['%CSI%', '$row', 'd'],       // Line position absolute
    'cursorMoveTo'              => ['%CSI%', '$y;$x', 'H'],
    'cursorMoveToPosition'      => ['%CSI%', '$y;$x', 'f'],     // really dont know the difference
    'cursorMoveTab'             => ['%CSI%', '$tab', 'I'],

    // todo: these could be grouped
    'eraseBelow'                => ['%CSI%', '0J'],     // default
    'eraseAbove'                => ['%CSI%', '1J'],
    'eraseAll'                  => ['%CSI%', '2J'],
    'eraseDisplay'              => ['%CSI%', '2J'],     // alias
    'eraseSavedLines'           => ['%CSI%', '3J'],
    'selectiveEraseBelow'       => ['%CSI%', '?0J'],    // default
    'selectiveEraseAbove'       => ['%CSI%', '?1J'],
    'selectiveEraseAll'         => ['%CSI%', '?2J'],
    'eraseLineToRight'          => ['%CSI%', '0K'],     // default
    'eraseLineToLeft'           => ['%CSI%', '1K'],
    'eraseLine'                 => ['%CSI%', '2K'],
    'selectiveEraseLineToRight' => ['%CSI%', '?0K'],     // default
    'selectiveEraseLineToLeft'  => ['%CSI%', '?1K'],
    'selectiveEraseLine'        => ['%CSI%', '?2K'],

    'eraseTabCurrentColumn'     => ['%CSI%', '0g'],
    'eraseTabAll'               => ['%CSI%', '3g'],

    'insertLine'                => ['%CSI%', '$times', 'L'],
    'deleteLine'                => ['%CSI%', '$times', 'M'],
    'deleteCharacters'          => ['%CSI%', '$times', 'P'],
    'scrollUp'                  => ['%CSI%', '$times', 'S'],
    'scrollDown'                => ['%CSI%', '$times', 'T'],

    // mouse tracking modes here
    /*
    CSI P s ; P s ; P s ; P s ; P s T
    Initiate highlight mouse tracking. Parameters are [func;startx;starty;firstrow;lastrow]. See the section Mouse Tracking.
    */

    'eraseCharacters'           => ['%CSI%', '$times', 'X'],
    'cursorMoveTabBackwards'    => ['%CSI%', '$times', 'Z'],
    'cursorMoveToCharacterPositionAbsolute'
                                => ['%CSI%', '$position', '`'],

    'repeatPrecedingGraphicCharacter'
                                => ['%CSI%', '$times', 'b'],

    // Primary DA
//    'requestPrimaryDeviceAttributes' => ['%CSI%', '0c'],      // this goes to STDIN (??) to be consumed

    /**
     * CSI P s c
     * Send Device Attributes (Primary DA)
     *
     * P s = 0 or omitted → request attributes from terminal. The response depends on the decTerminalID resource setting.
     * → CSI ? 1 ; 2 c (‘‘VT100 with Advanced Video Option’’)
     * → CSI ? 1 ; 0 c (‘‘VT101 with No Options’’)
     * → CSI ? 6 c (‘‘VT102’’)
     * → CSI ? 6 0 ; 1 ; 2 ; 6 ; 8 ; 9 ; 1 5 ; c (‘‘VT220’’)
     *
     * The VT100-style response parameters do not mean anything by themselves. VT220 parameters do, telling the host what features the terminal supports:
     * → 1 132-columns
     * → 2 Printer
     * → 6 Selective erase
     * → 8 User-defined keys
     * → 9 National replacement character sets
     * → 1 5 Technical characters
     * → 2 2 ANSI color, e.g., VT525
     * → 2 9 ANSI text locator (i.e., DEC Locator mode)
     */

//    'requestSecondaryDeviceAttributes' => ['%CSI%', '>0c'],

    // todo: these can be grouped or completely separated
    'setModeKeyboardAction'     => ['%CSI%', '2h'],
    'resetModeKeyboardAction'   => ['%CSI%', '2l'],
    'setModeInsert'             => ['%CSI%', '4h'],
    'setModeReplace'            => ['%CSI%', '4l'],
    'setModeSendReceive'        => ['%CSI%', '12h'],
    'setNormalLineFeed'         => ['%CSI%', '12l'],
    // group continues here - separated to indicate private mode
    'setPrivateModeApplicationCursorKeys'
                                => ['%CSI%', '?1h'],
    'setPrivateModeNormalCursorKeys'
                                => ['%CSI%', '?1l'],
    'setPrivateModeDesignateUSASCIIAndVT100'
                                => ['%CSI%', '?2h'],
    'setPrivateModeDesignateVT52'
                                => ['%CSI%', '?2l'],
    'setPrivateMode132Column'   => ['%CSI%', '?3h'],
    'setPrivateMode80Column'    => ['%CSI%', '?3l'],
    'setPrivateModeSmoothScroll'
                                => ['%CSI%', '?4h'],
    'setPrivateModeFastScroll'  => ['%CSI%', '?4l'],
    'setPrivateModeReverseVideo'
                                => ['%CSI%', '?5h'],
    'setPrivateModeNormalVideo' => ['%CSI%', '?5l'],
    'setPrivateModeOrigin'      => ['%CSI%', '?6h'],
    'setPrivateModeNormalCursor'
                                => ['%CSI%', '?6l'],
    'setPrivateModeWraparound'  => ['%CSI%', '?7h'],
    'setPrivateModeNoWraparound'
                                => ['%CSI%', '?7l'],
    'setPrivateModeAutoRepeatKeys'
                                => ['%CSI%', '?8h'],
    'setPrivateModeNoAutoRepeatKeys'
                                => ['%CSI%', '?8l'],
    'setPrivateModeTrackMouseOnButtonPress'
                                => ['%CSI%', '?9h'],        // mouse tracking
    'unsetPrivateModeTrackMouseOnButtonPress'
                                => ['%CSI%', '?9l'],        // mouse tracking
    'setPrivateModeShowToolbar' => ['%CSI%', '?10h'],      // rxvt
    'setPrivateModeHideToolbar' => ['%CSI%', '?10l'],      // rxvt
    'setPrivateModeStartBlinkingCursor'
                                => ['%CSI%', '?12h'],     // att610
    'setPrivateModeStopBlinkingCursor'
                                => ['%CSI%', '?12l'],     // att610
    'setPrivateModePrintFormFeed'
                                => ['%CSI%', '?18h'],
    'unsetPrivateModePrintFormFeed'
                                => ['%CSI%', '?18l'],
    'setPrivateModePrintExtentToFullScreen'
                                => ['%CSI%', '?19h'],
    'setPrivateModePrintExtentToScrollingRegion'
                                => ['%CSI%', '?19l'],
    'setPrivateModeShowCursor'  => ['%CSI%', '?25h'],
    'showCursor'                => ['%CSI%', '?25h'],   // alias
    'setPrivateModeHideCursor'  => ['%CSI%', '?25l'],
    'hideCursor'                => ['%CSI%', '?25l'],   // alias
    'setPrivateModeShowScrollbar'
                                => ['%CSI%', '?30h'],
    'setPrivateModeHideScrollbar'
                                => ['%CSI%', '?30l'],
    'setPrivateModeEnableFontShifting'
                                => ['%CSI%', '?35h'],      // rxvt
    'setPrivateModeDisableFontShifting'
                                => ['%CSI%', '?35l'],      // rxvt
    'setPrivateModeEnterTektronixMode'
                                => ['%CSI%', '?38h'],
    'setPrivateModeAllow80To132Mode'
                                => ['%CSI%', '?40h'],
    'setPrivateModeDisallow80To132Mode'
                                => ['%CSI%', '?40l'],
    'setPrivateModeMoreFix'     => ['%CSI%', '?41h'],
    'unsetPrivateModeMoreFix'   => ['%CSI%', '?41l'],
    'setPrivateModeEnableNationReplacementCharacterSets'
                                => ['%CSI%', '?42h'],
    'setPrivateModeDisableNationReplacementCharacterSets'
                                => ['%CSI%', '?42l'],
    'setPrivateModeTurnOnMarginBell'
                                => ['%CSI%', '?44h'],
    'setPrivateModeTurnOffMarginBell'
                                => ['%CSI%', '?44l'],
    'setPrivateModeReverseWrapAround'
                                => ['%CSI%', '?45h'],
    'unsetPrivateModeReverseWrapAround'
                                => ['%CSI%', '?45l'],
    'setPrivateModeStartLogging'
                                => ['%CSI%', '?46h'],
    'setPrivateModeStopLogging' => ['%CSI%', '?46l'],
    'setPrivateModeAlternateScreenBuffer'
                                => ['%CSI%', '?47h'],
    'setPrivateModeNormalScreenBuffer'
                                => ['%CSI%', '?47l'],
    'setPrivateModeApplicationKeypad'
                                => ['%CSI%', '?66h'],
    'setPrivateModeNumericKeypad'
                                => ['%CSI%', '?66l'],
    'setPrivateModeBackarrowSendsBackspace'
                                => ['%CSI%', '?67h'],
    'setPrivateModeBackarrowSendsDelete'
                                => ['%CSI%', '?67l'],
    'setPrivateModeTrackMouseOnPressAndRelease'
                                => ['%CSI%', '?1000h'],     // mouse tracking
    'unsetPrivateModeTrackMouseOnPressAndRelease'
                                => ['%CSI%', '?1000l'],     // mouse tracking
    'setPrivateModeTrackMouseHilite'
                                => ['%CSI%', '?1001h'],   // mouse tracking
    'unsetPrivateModeTrackMouseHilite'
                                => ['%CSI%', '?1001l'],   // mouse tracking
    'setPrivateModeTrackMouseCellMotion'
                                => ['%CSI%', '?1002h'], // mouse tracking
    'unsetPrivateModeTrackMouseCellMotion'
                                => ['%CSI%', '?1002l'], // mouse tracking
    'setPrivateModeTrackMouseAll'
                                => ['%CSI%', '?1003h'],  // mouse tracking
    'unsetPrivateModeTrackMouseAll'
                                => ['%CSI%', '?1003l'],  // mouse tracking
    'setPrivateModeTrackMouseFocus'
                                => ['%CSI%', '?1004h'],  // mouse tracking
    'unsetPrivateModeTrackMouseFocus'
                                => ['%CSI%', '?1004l'],  // mouse tracking
    'setPrivateModeTrackMouseSgrExt'
                                => ['%CSI%', '?1006h'],  // mouse tracking
    'unsetPrivateModeTrackMouseSgrExt'
                                => ['%CSI%', '?1006l'],  // mouse tracking
    'setPrivateModeScrollToBottomOnOutput'
                                => ['%CSI%', '?1010h'],  // rxvt
    'unsetPrivateModeScrollToBottomOnOutput'
                                => ['%CSI%', '?1010l'],  // rxvt
    'setPrivateModeScrollToBottomOnKeyPress'
                                => ['%CSI%', '?1011h'], // rxvt
    'unsetPrivateModeScrollToBottomOnKeyPress'
                                => ['%CSI%', '?1011l'], // rxvt
    'setPrivateModeEnableSpecialModifiersForAltAndNumLock'
                                => ['%CSI%', '?1035h'],
    'setPrivateModeDisableSpecialModifiersForAltAndNumLock'
                                => ['%CSI%', '?1035l'],
    'setPrivateModeSendEscapeWhenMeta'
                                => ['%CSI%', '?1036h'],
    'unsetPrivateModeSendEscapeWhenMeta'
                                => ['%CSI%', '?1036l'],
    'setPrivateModeSendDelFromKeypad'
                                => ['%CSI%', '?1037h'],
    'setPrivateModeSendVT220RemoveFromKeypad'
                                => ['%CSI%', '?1037l'],
    'setPrivateModeAlternateScreenBuffer1047'
                                => ['%CSI%', '?1047h'], // probably for compatibility?
    'setPrivateModeClearAndUseNormalScreenBuffer'
                                => ['%CSI%', '?1047l'], // probably for compatibility?
    'setPrivateModeSaveCursor'  => ['%CSI%', '?1048h'],
    'setPrivateModeRestoreCursor'
                                => ['%CSI%', '?1048l'],

    // mode 1049 -- this might be only applicable to terminfo-based apps rather than mode 1047
    // @see https://www.xfree86.org/current/ctlseqs.html
    'setPrivateModeSaveCursorAndEnterAlternateScreenBuffer'
                                => ['%CSI%', '?1049h'],
    'saveCursorAndEnterAlternateScreenBuffer'
                                => ['%CSI%', '?1049h'],     // alias
    'setPrivateModeRestoreCursorAndEnterNormalScreenBuffer'
                                => ['%CSI%', '?1049l'],
    'restoreCursorAndEnterNormalScreenBuffer'
                                => ['%CSI%', '?1049l'],     // alias
    'setPrivateModeSunFunctionKey'
                                => ['%CSI%', '?1051h'],
    'unsetPrivateModeSunFunctionKey'
                                => ['%CSI%', '?1051l'],
    'setPrivateModeHPFunctionKey'
                                => ['%CSI%', '?1052h'],
    'unsetPrivateModeHPFunctionKey'
                                => ['%CSI%', '?1052l'],
    'setPrivateModeSCOFunctionKey'
                                => ['%CSI%', '?1053h'],
    'unsetPrivateModeSCOFunctionKey'
                                => ['%CSI%', '?1053l'],
    'setPrivateModeLegacyKeyboardEmulation'
                                => ['%CSI%', '?1060h'],
    'unsetPrivateModeLegacyKeyboardEmulation'
                                => ['%CSI%', '?1060l'],
    'setPrivateModeSunEmulationOfVT220'
                                => ['%CSI%', '?1061h'],
    'unsetPrivateModeSunEmulationOfVT220'
                                => ['%CSI%', '?1061l'],
    'setPrivateModeBracketedPasteMode'
                                => ['%CSI%', '?2004h'],
    'unsetPrivateModeBracketedPasteMode'
                                => ['%CSI%', '?2004l'],

    // Media Copy
    'printScreen'               => ['%CSI%', '0i'],
    'turnOffPrinterControllerMode'
                                => ['%CSI%', '4i'],
    'turnOnPrinterControllerMode'
                                => ['%CSI%', '5i'],
    // Media Copy - DEC specific
    'printLineOnCursor'         => ['%CSI%', '?1i'],
    'turnOffAutoprint'          => ['%CSI%', '?4i'],
    'turnOnAutoprint'           => ['%CSI%', '?5i'],
    'printComposedDisplay'      => ['%CSI%', '?10i'],
    'printAllPages'             => ['%CSI%', '?11i'],

    // Character attributes and color
    // pending possible value objects

    'softReset'                 => ['%CSI%', '!p'],

    /*
        Set conformance level (DECSCL) Valid values for the first parameter:

        P s = 6 1 → VT100
        P s = 6 2 → VT200
        P s = 6 3 → VT300
        Valid values for the second parameter:
        P s = 0 → 8-bit controls
        P s = 1 → 7-bit controls (always set for VT100)
        P s = 2 → 8-bit controls
    */
    'setConformanceLevel'       => ['%CSI%', '$vtMode;$bitControl', '\"p'],

    // character protection attribute DECSCA
    // scrolling region DECSTBM
    // restore private mode values
    // change attributes in rectangular area
    // save cursor (ansi.sys)
    // save private mode values

    'windowDeiconify'           => ['%CSI%', '1t'],      // restore from minimize
    'windowIconify'             => ['%CSI%', '2t'],        // minimize
    'windowMove'                => ['%CSI%', '3;$x;$y', 't'],
    'windowResizePixels'        => ['%CSI%', '4;$height;$width', 't'],
    'windowToFront'             => ['%CSI%', '5t'],
    'windowToBack'              => ['%CSI%', '6t'],
    'windowRefresh'             => ['%CSI%', '7t'],
    'windowResizeCharacters'    => ['%CSI%', '8;$heightCharacters;$widthCharacters', 't'],

        // these seem to work only in Xterm (by default)
    'windowRestoreFromMaximized'
                                => ['%CSI%', '9;0t'],
    'windowMaximize'            => ['%CSI%', '9;1t'],
    'windowMaximizeVertically'  => ['%CSI%', '9;2t'],
    'windowMaximizeHorizontally'
                                => ['%CSI%', '9;3t'],
    'windowUndoFullScreen'      => ['%CSI%', '10;0t'],
    'windowFullScreen'          => ['%CSI%', '10;1t'],
    'windowFullScreenToggle'    => ['%CSI%', '10;2t'],

    // Styles & Colors

    'normal'                    => ['%CSI%', '0m'],
    'bold'                      => ['%CSI%', '1m'],
    'faint'                     => ['%CSI%', '2m'],
    'italic'                    => ['%CSI%', '3m'],
    'underline'                 => ['%CSI%', '4m'],
    'blink'                     => ['%CSI%', '5m'],
    'inverse'                   => ['%CSI%', '7m'],
    'invisible'                 => ['%CSI%', '8m'],
                // ECMA-48, 3rd
    'strikethrough'             => ['%CSI%', '9m'],
    'doubleUnderline'           => ['%CSI%', '21m'],
    'notBoldNotFaint'           => ['%CSI%', '22m'],
    'notItalic'                 => ['%CSI%', '23m'],
    'notUnderline'              => ['%CSI%', '24m'],
    'notBlink'                  => ['%CSI%', '25m'],
    'steady'                    => ['%CSI%', '25m'],        // alias
    'notInverse'                => ['%CSI%', '27m'],
    'positive'                  => ['%CSI%', '27m'],        // alias
    'notInvisible'              => ['%CSI%', '28m'],
    'visible'                   => ['%CSI%', '28m'],        // alias
    'notStrikethrough'          => ['%CSI%', '29m'],

                // Foreground
    'black'                     => ['%CSI%', '30m'],
    'red'                       => ['%CSI%', '31m'],
    'green'                     => ['%CSI%', '32m'],
    'yellow'                    => ['%CSI%', '33m'],
    'blue'                      => ['%CSI%', '34m'],
    'magenta'                   => ['%CSI%', '35m'],
    'cyan'                      => ['%CSI%', '36m'],
    'white'                     => ['%CSI%', '37m'],
                // ECMA-48, 3rd
    'default'                   => ['%CSI%', '39m'],

                // Background
    'bgBlack'                   => ['%CSI%', '40m'],
    'bgRed'                     => ['%CSI%', '41m'],
    'bgGreen'                   => ['%CSI%', '42m'],
    'bgYellow'                  => ['%CSI%', '43m'],
    'bgBlue'                    => ['%CSI%', '44m'],
    'bgMagenta'                 => ['%CSI%', '45m'],
    'bgCyan'                    => ['%CSI%', '46m'],
    'bgWhite'                   => ['%CSI%', '47m'],
                // ECMA-48, 3rd
    'bgDefault'                 => ['%CSI%', '49m'],

                // Bright (aixterm)
    'brightBlack'               => ['%CSI%', '90m'],
    'brightRed'                 => ['%CSI%', '91m'],
    'brightGreen'               => ['%CSI%', '92m'],
    'brightYellow'              => ['%CSI%', '93m'],
    'brightBlue'                => ['%CSI%', '94m'],
    'brightMagenta'             => ['%CSI%', '95m'],
    'brightCyan'                => ['%CSI%', '96m'],
    'brightWhite'               => ['%CSI%', '97m'],

                // Bright background (aixterm)
    'brightBgBlack'             => ['%CSI%', '100m'],     // rxvt uses this as default fg & bg
    'brightBgRed'               => ['%CSI%', '101m'],
    'brightBgGreen'             => ['%CSI%', '102m'],
    'brightBgYellow'            => ['%CSI%', '103m'],
    'brightBgBlue'              => ['%CSI%', '104m'],
    'brightBgMagenta'           => ['%CSI%', '105m'],
    'brightBgCyan'              => ['%CSI%', '106m'],
    'brightBgWhite'             => ['%CSI%', '107m'],

                // 88/256 colors (i for indexed)
    'fgi'                       => ['%CSI%', '38;5;$color', 'm'],
    'bgi'                       => ['%CSI%', '48;5;$color', 'm'],
                // 16-bit colors
    'rgb'                       => ['%CSI%', '38;2;$red;$green;$blue', 'm'],
    'bgRgb'                     => ['%CSI%', '48;2;$red;$green;$blue', 'm'],

    // multiple arguments -- why we have this too?
    'moveCursorTo'              => ['%CSI%', '$y;$x', 'H'],
];

$arguments = [
    'insertBlankCharacters'     => 'int $count',

    'cursorUp'                  => 'int $times',
    'cursorDown'                => 'int $times',
    'cursorForward'             => 'int $times',
    'cursorBackward'            => 'int $times',
    'cursorNextLine'            => 'int $times',
    'cursorPrecedingLine'       => 'int $times',
    'cursorMoveToColumn'        => 'int $column',
    'cursorMoveToRow'           => 'int $row',       // Line position absolute
    'cursorMoveTo'              => 'int $y, int $x',
    'cursorMoveToPosition'      => 'int $y, int $x',     // really dont know the difference
    'cursorMoveTab'             => 'int $tab',

    'insertLine'                => 'int $times',
    'deleteLine'                => 'int $times',
    'deleteCharacters'          => 'int $times',
    'scrollUp'                  => 'int $times',
    'scrollDown'                => 'int $times',

    'eraseCharacters'           => 'int $times',
    'cursorMoveTabBackwards'    => 'int $times',
    'cursorMoveToCharacterPositionAbsolute'
                                => 'int $position',

    'repeatPrecedingGraphicCharacter'
                                => 'int $times',

    'setConformanceLevel'       => 'int $vtMode, int $bitControl',

    'windowMove'                => 'int $x, int $y',
    'windowResizePixels'        => 'int $height, int $width',
    'windowResizeCharacters'    => 'int $heightCharacters, int $widthCharacters',

    'fgi'                       => 'int $color',
    'bgi'                       => 'int $color',
    'rgb'                       => 'int $red, int $green, int $blue',
    'bgRgb'                     => 'int $red, int $green, int $blue',

    // multiple arguments -- why we have this too?
    'moveCursorTo'              => 'int $y, int $x',
];

echo '<?php' . PHP_EOL;
?>

namespace Aspectus\Terminal;

use Amp\DeferredFuture;
use Aspectus\Terminal\Event\EscapeSequenceEvent;
use Aspectus\Terminal\Event\InputEvent;
use Aspectus\Terminal\Value\PrimaryDeviceAttributes;
use Aspectus\Terminal\Value\SecondaryDeviceAttributes;
use Aspectus\Terminal\Value\TerminalParameters;
use Aspectus\Terminal\Xterm\ControlDriver\Xterm7BitControlDriver;
use Aspectus\Terminal\Xterm\ControlDriver\Xterm8BitControlDriver;
use Aspectus\Terminal\Xterm\Event\XtermEventFactory;

class Xterm
{
    /**
     * We keep a buffer to minimize how many times we call fwrite. Is that correct or we should not care about
     * fwrite and the buffer is worse?
     * @todo Use php bench here
     *
     * @var string
     */
    private string $outBuffer = '';

    public function __construct(
        private TerminalDevice $device = new TerminalDevice(),
        private Xterm7BitControlDriver|Xterm8BitControlDriver $driver = new Xterm7BitControlDriver(),
        private XtermEventFactory $eventFactory = new XtermEventFactory()
    ) {
        $this->device->setEventFactory($this->eventFactory);
    }

    public function close(): void
    {
        $this->device->free();
    }

    public function getBuffered(bool $clearAfter = true): string
    {
        $return = $this->outBuffer;
        if ($clearAfter) {
            $this->outBuffer = '';
        }
        return $return;
    }

    public function __toString(): string
    {
        return $this->getBuffered(true);
    }

    public function write(string $data): self
    {
        $this->outBuffer .= $data;
        return $this;
    }

    public function error(string $data): self
    {
        $this->device->error($data);
        return $this;
    }

    /**
     * @template E of InputEvent
     * @template C of callable(E):?true|\Closure(E):?true
     *
     * @param class-string<E> $event
     * @param C $listener
     * @return self
     */
    public function subscribe(string $event, callable $listener): self
    {
        $this->device->subscribe($event, $listener);
        return $this;
    }

    public function flush(): self
    {
        $this->device->write($this->outBuffer);
        $this->outBuffer = '';
        return $this;
    }

    public function requestPrimaryDeviceAttributes(): PrimaryDeviceAttributes
    {
        $deferred = new DeferredFuture();

        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete(PrimaryDeviceAttributes::fromString($event->data));
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '0c');
        return $deferred->getFuture()->await();
    }

    public function requestSecondaryDeviceAttributes(): SecondaryDeviceAttributes
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete(SecondaryDeviceAttributes::fromString($event->data));
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        // also `CSI Ps x` ?
        $this->device->write($this->driver::CSI . '>0c');
        return $deferred->getFuture()->await();
    }

    public function requestTerminalParameters(): TerminalParameters
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete(TerminalParameters::fromString($event->data));
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '>0x');
        return $deferred->getFuture()->await();
    }

    public function reportDeviceStatus(): bool
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '5n');

        // maybe missing something here but when its 0n then its "OK"
        return $deferred->getFuture()->await() === $this->driver::CSI . '0n';
    }

    public function reportCursorPosition(): array
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '6n');
        $return = $deferred->getFuture()->await();

        // what if there is nothing? will it be null ?

        $row = strtok($return, ';');
        $column = strtok('R');

        return [$row, $column];
    }

    public function reportPrinterStatus(): bool
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '15n');
        return $deferred->getFuture()->await() === $this->driver::CSI . '?10n'; // for ready
    }


    public function reportWindowPosition(): array
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '13t');

        $return = $deferred->getFuture()->await();

        strtok($return, ';');
        return [strtok(';'), strtok('t')];
    }

    public function reportTextAreaPosition(): array
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '13;2t');

        $return = $deferred->getFuture()->await();

        strtok($return, ';');
        return [strtok(';'), strtok('t')];
    }


    public function reportTextAreaSizePixels(): array
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '14t');

        $return = $deferred->getFuture()->await();

        strtok($return, ';');
        return [strtok(';'), strtok('t')];
    }

    public function reportWindowSizePixels(): array
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '14;2t');

        $return = $deferred->getFuture()->await();

        strtok($return, ';');
        return [strtok(';'), strtok('t')];
    }

    public function reportScreenSizePixels(): array
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '15t');

        $return = $deferred->getFuture()->await();

        strtok($return, ';');
        return [strtok(';'), strtok('t')];
    }

    public function reportCharacterSizePixels(): array
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '16t');

        $return = $deferred->getFuture()->await();

        strtok($return, ';');
        return [strtok(';'), strtok('t')];
    }

    public function reportTextAreaSizeCharacters(): array
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '18t');

        $return = $deferred->getFuture()->await();

        strtok($return, ';');
        return [strtok(';'), strtok('t')];
    }

    public function reportScreenSizeCharacters(): array
    {
        $deferred = new DeferredFuture();
        // todo: add prepend method
        $this->device->subscribe(
            EscapeSequenceEvent::class,
            function (EscapeSequenceEvent $event) use ($deferred) {
                try {
                    $deferred->complete($event->data);
                } catch (\Throwable $e) {
                    $deferred->error($e);
                }

                return true;        // signal to un-register listener
            }
        );

        $this->device->write($this->driver::CSI . '19t');

        $return = $deferred->getFuture()->await();

        strtok($return, ';');
        return [strtok(';'), strtok('t')];
    }

    // missing the DeviceStatusReports for DEC as they dont seem to work always?
<?php

function method(string $name, array|string $sequence) {
    global $arguments;

    $argumentsString = '(): self';
    if (isset($arguments[$name])) {
        $argumentsString = '(' . $arguments[$name] . '): self';
    }

    if (!is_array($sequence)) {
        $code = 'return $this->write("' . $sequence . '");';
    } else {
        $first = str_replace('%CSI%', '$this->driver::CSI', $sequence[0]);
        $second = (isset($sequence[1]) ? '"' . $sequence[1] . '"' : '')
            . (isset($sequence[2]) ? ' . \'' . $sequence[2] . '\'' : '');
        $code = 'return $this->write(' . $first . ' . ' . $second . ');';
    }

    $signature = $name . $argumentsString;

    return <<<METHOD
    public function $signature
    {
        $code
    }

METHOD;
}

$methodsCode = '';
foreach ($methods as $methodName => $signature) {
    echo PHP_EOL . method($methodName, $signature);
}
?>
}
