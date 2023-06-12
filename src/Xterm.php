<?php

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

        $this->device->write('$this->driver::CSI . ' . '6n');
        $return = $deferred->getFuture()->await();

        // what if there is nothing? will it be null ?

        $return = str_replace($this->driver::CSI, '', $return);
        $row = strtok(';');
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

    // missing the DeviceStatusReports for DEC as they dont seem to work always?

    public function bell(): self
    {
        return $this->write("\x07");
    }

    public function backspace(): self
    {
        return $this->write("\x08");
    }

    public function cr(): self
    {
        return $this->write("\x0d");
    }

    public function lf(): self
    {
        return $this->write("\x0a");
    }

    public function s7c1t(): self
    {
        return $this->write("\x1b F");
    }

    public function s8c1t(): self
    {
        return $this->write("\x1b G");
    }

    public function ansiLevel1(): self
    {
        return $this->write("\x1b L");
    }

    public function ansiLevel2(): self
    {
        return $this->write("\x1b M");
    }

    public function ansiLevel3(): self
    {
        return $this->write("\x1b N");
    }

    public function doubleHeightTopHalfLine(): self
    {
        return $this->write("\x1b#3");
    }

    public function doubleHeightBottomHalfLine(): self
    {
        return $this->write("\x1b#4");
    }

    public function singleWidthLine(): self
    {
        return $this->write("\x1b#5");
    }

    public function doubleWidthLine(): self
    {
        return $this->write("\x1b#6");
    }

    public function screenAlignmentTest(): self
    {
        return $this->write("\x1b#8");
    }

    public function selectDefaultCharset(): self
    {
        return $this->write("\x1b%@");
    }

    public function selectUTF8Charset(): self
    {
        return $this->write("\x1b%G");
    }

    public function selectG0Charset(): self
    {
        return $this->write("\x1b(");
    }

    public function selectG1Charset(): self
    {
        return $this->write("\x1b)");
    }

    public function selectG2Charset(): self
    {
        return $this->write("\x1b*");
    }

    public function selectG3Charset(): self
    {
        return $this->write("\x1b+");
    }

    public function selectDECSpecialAndLineDrawingCharset(): self
    {
        return $this->write("\x1b+0");
    }

    public function selectUKCharset(): self
    {
        return $this->write("\x1b+A");
    }

    public function selectUSCharset(): self
    {
        return $this->write("\x1b+B");
    }

    public function selectDutchCharset(): self
    {
        return $this->write("\x1b+4");
    }

    public function selectFinnishCharset(): self
    {
        return $this->write("\x1b+C");
    }

    public function selectFrenchCharset(): self
    {
        return $this->write("\x1b+R");
    }

    public function selectFrenchCanadianCharset(): self
    {
        return $this->write("\x1b+Q");
    }

    public function selectGermanCharset(): self
    {
        return $this->write("\x1b+K");
    }

    public function selectItalianCharset(): self
    {
        return $this->write("\x1b+Y");
    }

    public function selectNorwegianCharset(): self
    {
        return $this->write("\x1b+E");
    }

    public function selectSpanishCharset(): self
    {
        return $this->write("\x1b+Z");
    }

    public function selectSwedishCharset(): self
    {
        return $this->write("\x1b+H");
    }

    public function selectSwissCharset(): self
    {
        return $this->write("\x1b+=");
    }

    public function saveCursor(): self
    {
        return $this->write("\x1b7");
    }

    public function restoreCursor(): self
    {
        return $this->write("\x1b8");
    }

    public function setApplicationKeypad(): self
    {
        return $this->write("=");
    }

    public function setNormalKeypad(): self
    {
        return $this->write(">");
    }

    public function moveCursorToLowerLeft(): self
    {
        return $this->write("\x1bF");
    }

    public function reset(): self
    {
        return $this->write("c");
    }

    public function lockMemory(): self
    {
        return $this->write("\x1bl");
    }

    public function unlockMemory(): self
    {
        return $this->write("\x1bm");
    }

    public function invokeG2AsGL(): self
    {
        return $this->write("\x1bn");
    }

    public function invokeG3AsGL(): self
    {
        return $this->write("\x1bo");
    }

    public function invokeG3AsGR(): self
    {
        return $this->write("\x1b|");
    }

    public function invokeG2AsGR(): self
    {
        return $this->write("\x1b}");
    }

    public function invokeG1AsGR(): self
    {
        return $this->write("\x1b~");
    }

    public function insertBlankCharacters(int $count): self
    {
        return $this->write($this->driver::CSI . "$count" . '@');
    }

    public function cursorUp(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'A');
    }

    public function cursorDown(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'B');
    }

    public function cursorForward(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'C');
    }

    public function cursorBackward(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'D');
    }

    public function cursorNextLine(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'E');
    }

    public function cursorPrecedingLine(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'F');
    }

    public function cursorMoveToColumn(int $column): self
    {
        return $this->write($this->driver::CSI . "$column" . 'G');
    }

    public function cursorMoveToRow(int $row): self
    {
        return $this->write($this->driver::CSI . "$row" . 'd');
    }

    public function cursorMoveTo(int $y, int $x): self
    {
        return $this->write($this->driver::CSI . "$y;$x" . 'H');
    }

    public function cursorMoveToPosition(int $y, int $x): self
    {
        return $this->write($this->driver::CSI . "$y;$x" . 'f');
    }

    public function cursorMoveTab(int $tab): self
    {
        return $this->write($this->driver::CSI . "$tab" . 'I');
    }

    public function eraseBelow(): self
    {
        return $this->write($this->driver::CSI . "0J");
    }

    public function eraseAbove(): self
    {
        return $this->write($this->driver::CSI . "1J");
    }

    public function eraseAll(): self
    {
        return $this->write($this->driver::CSI . "2J");
    }

    public function eraseDisplay(): self
    {
        return $this->write($this->driver::CSI . "2J");
    }

    public function eraseSavedLines(): self
    {
        return $this->write($this->driver::CSI . "3J");
    }

    public function selectiveEraseBelow(): self
    {
        return $this->write($this->driver::CSI . "?0J");
    }

    public function selectiveEraseAbove(): self
    {
        return $this->write($this->driver::CSI . "?1J");
    }

    public function selectiveEraseAll(): self
    {
        return $this->write($this->driver::CSI . "?2J");
    }

    public function eraseLineToRight(): self
    {
        return $this->write($this->driver::CSI . "0K");
    }

    public function eraseLineToLeft(): self
    {
        return $this->write($this->driver::CSI . "1K");
    }

    public function eraseLine(): self
    {
        return $this->write($this->driver::CSI . "2K");
    }

    public function selectiveEraseLineToRight(): self
    {
        return $this->write($this->driver::CSI . "?0K");
    }

    public function selectiveEraseLineToLeft(): self
    {
        return $this->write($this->driver::CSI . "?1K");
    }

    public function selectiveEraseLine(): self
    {
        return $this->write($this->driver::CSI . "?2K");
    }

    public function eraseTabCurrentColumn(): self
    {
        return $this->write($this->driver::CSI . "0g");
    }

    public function eraseTabAll(): self
    {
        return $this->write($this->driver::CSI . "3g");
    }

    public function insertLine(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'L');
    }

    public function deleteLine(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'M');
    }

    public function deleteCharacters(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'P');
    }

    public function scrollUp(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'S');
    }

    public function scrollDown(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'T');
    }

    public function eraseCharacters(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'X');
    }

    public function cursorMoveTabBackwards(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'Z');
    }

    public function cursorMoveToCharacterPositionAbsolute(int $position): self
    {
        return $this->write($this->driver::CSI . "$position" . '`');
    }

    public function repeatPrecedingGraphicCharacter(int $times): self
    {
        return $this->write($this->driver::CSI . "$times" . 'b');
    }

    public function setModeKeyboardAction(): self
    {
        return $this->write($this->driver::CSI . "2h");
    }

    public function resetModeKeyboardAction(): self
    {
        return $this->write($this->driver::CSI . "2l");
    }

    public function setModeInsert(): self
    {
        return $this->write($this->driver::CSI . "4h");
    }

    public function setModeReplace(): self
    {
        return $this->write($this->driver::CSI . "4l");
    }

    public function setModeSendReceive(): self
    {
        return $this->write($this->driver::CSI . "12h");
    }

    public function setNormalLineFeed(): self
    {
        return $this->write($this->driver::CSI . "12l");
    }

    public function setPrivateModeApplicationCursorKeys(): self
    {
        return $this->write($this->driver::CSI . "?1h");
    }

    public function setPrivateModeNormalCursorKeys(): self
    {
        return $this->write($this->driver::CSI . "?1l");
    }

    public function setPrivateModeDesignateUSASCIIAndVT100(): self
    {
        return $this->write($this->driver::CSI . "?2h");
    }

    public function setPrivateModeDesignateVT52(): self
    {
        return $this->write($this->driver::CSI . "?2l");
    }

    public function setPrivateMode132Column(): self
    {
        return $this->write($this->driver::CSI . "?3h");
    }

    public function setPrivateMode80Column(): self
    {
        return $this->write($this->driver::CSI . "?3l");
    }

    public function setPrivateModeSmoothScroll(): self
    {
        return $this->write($this->driver::CSI . "?4h");
    }

    public function setPrivateModeFastScroll(): self
    {
        return $this->write($this->driver::CSI . "?4l");
    }

    public function setPrivateModeReverseVideo(): self
    {
        return $this->write($this->driver::CSI . "?5h");
    }

    public function setPrivateModeNormalVideo(): self
    {
        return $this->write($this->driver::CSI . "?5l");
    }

    public function setPrivateModeOrigin(): self
    {
        return $this->write($this->driver::CSI . "?6h");
    }

    public function setPrivateModeNormalCursor(): self
    {
        return $this->write($this->driver::CSI . "?6l");
    }

    public function setPrivateModeWraparound(): self
    {
        return $this->write($this->driver::CSI . "?7h");
    }

    public function setPrivateModeNoWraparound(): self
    {
        return $this->write($this->driver::CSI . "?7l");
    }

    public function setPrivateModeAutoRepeatKeys(): self
    {
        return $this->write($this->driver::CSI . "?8h");
    }

    public function setPrivateModeNoAutoRepeatKeys(): self
    {
        return $this->write($this->driver::CSI . "?8l");
    }

    public function setPrivateModeTrackMouseOnButtonPress(): self
    {
        return $this->write($this->driver::CSI . "?9h");
    }

    public function unsetPrivateModeTrackMouseOnButtonPress(): self
    {
        return $this->write($this->driver::CSI . "?9l");
    }

    public function setPrivateModeShowToolbar(): self
    {
        return $this->write($this->driver::CSI . "?10h");
    }

    public function setPrivateModeHideToolbar(): self
    {
        return $this->write($this->driver::CSI . "?10l");
    }

    public function setPrivateModeStartBlinkingCursor(): self
    {
        return $this->write($this->driver::CSI . "?12h");
    }

    public function setPrivateModeStopBlinkingCursor(): self
    {
        return $this->write($this->driver::CSI . "?12l");
    }

    public function setPrivateModePrintFormFeed(): self
    {
        return $this->write($this->driver::CSI . "?18h");
    }

    public function unsetPrivateModePrintFormFeed(): self
    {
        return $this->write($this->driver::CSI . "?18l");
    }

    public function setPrivateModePrintExtentToFullScreen(): self
    {
        return $this->write($this->driver::CSI . "?19h");
    }

    public function setPrivateModePrintExtentToScrollingRegion(): self
    {
        return $this->write($this->driver::CSI . "?19l");
    }

    public function setPrivateModeShowCursor(): self
    {
        return $this->write($this->driver::CSI . "?25h");
    }

    public function showCursor(): self
    {
        return $this->write($this->driver::CSI . "?25h");
    }

    public function setPrivateModeHideCursor(): self
    {
        return $this->write($this->driver::CSI . "?25l");
    }

    public function hideCursor(): self
    {
        return $this->write($this->driver::CSI . "?25l");
    }

    public function setPrivateModeShowScrollbar(): self
    {
        return $this->write($this->driver::CSI . "?30h");
    }

    public function setPrivateModeHideScrollbar(): self
    {
        return $this->write($this->driver::CSI . "?30l");
    }

    public function setPrivateModeEnableFontShifting(): self
    {
        return $this->write($this->driver::CSI . "?35h");
    }

    public function setPrivateModeDisableFontShifting(): self
    {
        return $this->write($this->driver::CSI . "?35l");
    }

    public function setPrivateModeEnterTektronixMode(): self
    {
        return $this->write($this->driver::CSI . "?38h");
    }

    public function setPrivateModeAllow80To132Mode(): self
    {
        return $this->write($this->driver::CSI . "?40h");
    }

    public function setPrivateModeDisallow80To132Mode(): self
    {
        return $this->write($this->driver::CSI . "?40l");
    }

    public function setPrivateModeMoreFix(): self
    {
        return $this->write($this->driver::CSI . "?41h");
    }

    public function unsetPrivateModeMoreFix(): self
    {
        return $this->write($this->driver::CSI . "?41l");
    }

    public function setPrivateModeEnableNationReplacementCharacterSets(): self
    {
        return $this->write($this->driver::CSI . "?42h");
    }

    public function setPrivateModeDisableNationReplacementCharacterSets(): self
    {
        return $this->write($this->driver::CSI . "?42l");
    }

    public function setPrivateModeTurnOnMarginBell(): self
    {
        return $this->write($this->driver::CSI . "?44h");
    }

    public function setPrivateModeTurnOffMarginBell(): self
    {
        return $this->write($this->driver::CSI . "?44l");
    }

    public function setPrivateModeReverseWrapAround(): self
    {
        return $this->write($this->driver::CSI . "?45h");
    }

    public function unsetPrivateModeReverseWrapAround(): self
    {
        return $this->write($this->driver::CSI . "?45l");
    }

    public function setPrivateModeStartLogging(): self
    {
        return $this->write($this->driver::CSI . "?46h");
    }

    public function setPrivateModeStopLogging(): self
    {
        return $this->write($this->driver::CSI . "?46l");
    }

    public function setPrivateModeAlternateScreenBuffer(): self
    {
        return $this->write($this->driver::CSI . "?47h");
    }

    public function setPrivateModeNormalScreenBuffer(): self
    {
        return $this->write($this->driver::CSI . "?47l");
    }

    public function setPrivateModeApplicationKeypad(): self
    {
        return $this->write($this->driver::CSI . "?66h");
    }

    public function setPrivateModeNumericKeypad(): self
    {
        return $this->write($this->driver::CSI . "?66l");
    }

    public function setPrivateModeBackarrowSendsBackspace(): self
    {
        return $this->write($this->driver::CSI . "?67h");
    }

    public function setPrivateModeBackarrowSendsDelete(): self
    {
        return $this->write($this->driver::CSI . "?67l");
    }

    public function setPrivateModeTrackMouseOnPressAndRelease(): self
    {
        return $this->write($this->driver::CSI . "?1000h");
    }

    public function unsetPrivateModeTrackMouseOnPressAndRelease(): self
    {
        return $this->write($this->driver::CSI . "?1000l");
    }

    public function setPrivateModeTrackMouseHilite(): self
    {
        return $this->write($this->driver::CSI . "?1001h");
    }

    public function unsetPrivateModeTrackMouseHilite(): self
    {
        return $this->write($this->driver::CSI . "?1001l");
    }

    public function setPrivateModeTrackMouseCellMotion(): self
    {
        return $this->write($this->driver::CSI . "?1002h");
    }

    public function unsetPrivateModeTrackMouseCellMotion(): self
    {
        return $this->write($this->driver::CSI . "?1002l");
    }

    public function setPrivateModeTrackMouseAll(): self
    {
        return $this->write($this->driver::CSI . "?1003h");
    }

    public function unsetPrivateModeTrackMouseAll(): self
    {
        return $this->write($this->driver::CSI . "?1003l");
    }

    public function setPrivateModeTrackMouseFocus(): self
    {
        return $this->write($this->driver::CSI . "?1004h");
    }

    public function unsetPrivateModeTrackMouseFocus(): self
    {
        return $this->write($this->driver::CSI . "?1004l");
    }

    public function setPrivateModeTrackMouseSgrExt(): self
    {
        return $this->write($this->driver::CSI . "?1006h");
    }

    public function unsetPrivateModeTrackMouseSgrExt(): self
    {
        return $this->write($this->driver::CSI . "?1006l");
    }

    public function setPrivateModeScrollToBottomOnOutput(): self
    {
        return $this->write($this->driver::CSI . "?1010h");
    }

    public function unsetPrivateModeScrollToBottomOnOutput(): self
    {
        return $this->write($this->driver::CSI . "?1010l");
    }

    public function setPrivateModeScrollToBottomOnKeyPress(): self
    {
        return $this->write($this->driver::CSI . "?1011h");
    }

    public function unsetPrivateModeScrollToBottomOnKeyPress(): self
    {
        return $this->write($this->driver::CSI . "?1011l");
    }

    public function setPrivateModeEnableSpecialModifiersForAltAndNumLock(): self
    {
        return $this->write($this->driver::CSI . "?1035h");
    }

    public function setPrivateModeDisableSpecialModifiersForAltAndNumLock(): self
    {
        return $this->write($this->driver::CSI . "?1035l");
    }

    public function setPrivateModeSendEscapeWhenMeta(): self
    {
        return $this->write($this->driver::CSI . "?1036h");
    }

    public function unsetPrivateModeSendEscapeWhenMeta(): self
    {
        return $this->write($this->driver::CSI . "?1036l");
    }

    public function setPrivateModeSendDelFromKeypad(): self
    {
        return $this->write($this->driver::CSI . "?1037h");
    }

    public function setPrivateModeSendVT220RemoveFromKeypad(): self
    {
        return $this->write($this->driver::CSI . "?1037l");
    }

    public function setPrivateModeAlternateScreenBuffer1047(): self
    {
        return $this->write($this->driver::CSI . "?1047h");
    }

    public function setPrivateModeClearAndUseNormalScreenBuffer(): self
    {
        return $this->write($this->driver::CSI . "?1047l");
    }

    public function setPrivateModeSaveCursor(): self
    {
        return $this->write($this->driver::CSI . "?1048h");
    }

    public function setPrivateModeRestoreCursor(): self
    {
        return $this->write($this->driver::CSI . "?1048l");
    }

    public function setPrivateModeSaveCursorAndEnterAlternateScreenBuffer(): self
    {
        return $this->write($this->driver::CSI . "?1049h");
    }

    public function saveCursorAndEnterAlternateScreenBuffer(): self
    {
        return $this->write($this->driver::CSI . "?1049h");
    }

    public function setPrivateModeRestoreCursorAndEnterNormalScreenBuffer(): self
    {
        return $this->write($this->driver::CSI . "?1049l");
    }

    public function restoreCursorAndEnterNormalScreenBuffer(): self
    {
        return $this->write($this->driver::CSI . "?1049l");
    }

    public function setPrivateModeSunFunctionKey(): self
    {
        return $this->write($this->driver::CSI . "?1051h");
    }

    public function unsetPrivateModeSunFunctionKey(): self
    {
        return $this->write($this->driver::CSI . "?1051l");
    }

    public function setPrivateModeHPFunctionKey(): self
    {
        return $this->write($this->driver::CSI . "?1052h");
    }

    public function unsetPrivateModeHPFunctionKey(): self
    {
        return $this->write($this->driver::CSI . "?1052l");
    }

    public function setPrivateModeSCOFunctionKey(): self
    {
        return $this->write($this->driver::CSI . "?1053h");
    }

    public function unsetPrivateModeSCOFunctionKey(): self
    {
        return $this->write($this->driver::CSI . "?1053l");
    }

    public function setPrivateModeLegacyKeyboardEmulation(): self
    {
        return $this->write($this->driver::CSI . "?1060h");
    }

    public function unsetPrivateModeLegacyKeyboardEmulation(): self
    {
        return $this->write($this->driver::CSI . "?1060l");
    }

    public function setPrivateModeSunEmulationOfVT220(): self
    {
        return $this->write($this->driver::CSI . "?1061h");
    }

    public function unsetPrivateModeSunEmulationOfVT220(): self
    {
        return $this->write($this->driver::CSI . "?1061l");
    }

    public function setPrivateModeBracketedPasteMode(): self
    {
        return $this->write($this->driver::CSI . "?2004h");
    }

    public function unsetPrivateModeBracketedPasteMode(): self
    {
        return $this->write($this->driver::CSI . "?2004l");
    }

    public function printScreen(): self
    {
        return $this->write($this->driver::CSI . "0i");
    }

    public function turnOffPrinterControllerMode(): self
    {
        return $this->write($this->driver::CSI . "4i");
    }

    public function turnOnPrinterControllerMode(): self
    {
        return $this->write($this->driver::CSI . "5i");
    }

    public function printLineOnCursor(): self
    {
        return $this->write($this->driver::CSI . "?1i");
    }

    public function turnOffAutoprint(): self
    {
        return $this->write($this->driver::CSI . "?4i");
    }

    public function turnOnAutoprint(): self
    {
        return $this->write($this->driver::CSI . "?5i");
    }

    public function printComposedDisplay(): self
    {
        return $this->write($this->driver::CSI . "?10i");
    }

    public function printAllPages(): self
    {
        return $this->write($this->driver::CSI . "?11i");
    }

    public function softReset(): self
    {
        return $this->write($this->driver::CSI . "!p");
    }

    public function setConformanceLevel(int $vtMode, int $bitControl): self
    {
        return $this->write($this->driver::CSI . "$vtMode;$bitControl" . '\"p');
    }

    public function windowDeiconify(): self
    {
        return $this->write($this->driver::CSI . "1t");
    }

    public function windowIconify(): self
    {
        return $this->write($this->driver::CSI . "2t");
    }

    public function windowMove(int $x, int $y): self
    {
        return $this->write($this->driver::CSI . "3;$x;$y" . 't');
    }

    public function windowResizePixels(int $height, int $width): self
    {
        return $this->write($this->driver::CSI . "4;$height;$width" . 't');
    }

    public function windowToFront(): self
    {
        return $this->write($this->driver::CSI . "5t");
    }

    public function windowToBack(): self
    {
        return $this->write($this->driver::CSI . "6t");
    }

    public function windowRefresh(): self
    {
        return $this->write($this->driver::CSI . "7t");
    }

    public function windowResizeCharacters(int $heightCharacters, int $widthCharacters): self
    {
        return $this->write($this->driver::CSI . "8;$heightCharacters;$widthCharacters" . 't');
    }

    public function windowRestoreFromMaximized(): self
    {
        return $this->write($this->driver::CSI . "9;0t");
    }

    public function windowMaximize(): self
    {
        return $this->write($this->driver::CSI . "9;1t");
    }

    public function windowMaximizeVertically(): self
    {
        return $this->write($this->driver::CSI . "9;2t");
    }

    public function windowMaximizeHorizontally(): self
    {
        return $this->write($this->driver::CSI . "9;3t");
    }

    public function windowUndoFullScreen(): self
    {
        return $this->write($this->driver::CSI . "10;0t");
    }

    public function windowFullScreen(): self
    {
        return $this->write($this->driver::CSI . "10;1t");
    }

    public function windowFullScreenToggle(): self
    {
        return $this->write($this->driver::CSI . "10;2t");
    }

    public function normal(): self
    {
        return $this->write($this->driver::CSI . "0m");
    }

    public function bold(): self
    {
        return $this->write($this->driver::CSI . "1m");
    }

    public function faint(): self
    {
        return $this->write($this->driver::CSI . "2m");
    }

    public function italic(): self
    {
        return $this->write($this->driver::CSI . "3m");
    }

    public function underline(): self
    {
        return $this->write($this->driver::CSI . "4m");
    }

    public function blink(): self
    {
        return $this->write($this->driver::CSI . "5m");
    }

    public function inverse(): self
    {
        return $this->write($this->driver::CSI . "7m");
    }

    public function invisible(): self
    {
        return $this->write($this->driver::CSI . "8m");
    }

    public function strikethrough(): self
    {
        return $this->write($this->driver::CSI . "9m");
    }

    public function doubleUnderline(): self
    {
        return $this->write($this->driver::CSI . "21m");
    }

    public function notBoldNotFaint(): self
    {
        return $this->write($this->driver::CSI . "22m");
    }

    public function notItalic(): self
    {
        return $this->write($this->driver::CSI . "23m");
    }

    public function notUnderline(): self
    {
        return $this->write($this->driver::CSI . "24m");
    }

    public function notBlink(): self
    {
        return $this->write($this->driver::CSI . "25m");
    }

    public function steady(): self
    {
        return $this->write($this->driver::CSI . "25m");
    }

    public function notInverse(): self
    {
        return $this->write($this->driver::CSI . "27m");
    }

    public function positive(): self
    {
        return $this->write($this->driver::CSI . "27m");
    }

    public function notInvisible(): self
    {
        return $this->write($this->driver::CSI . "28m");
    }

    public function visible(): self
    {
        return $this->write($this->driver::CSI . "28m");
    }

    public function notStrikethrough(): self
    {
        return $this->write($this->driver::CSI . "29m");
    }

    public function black(): self
    {
        return $this->write($this->driver::CSI . "30m");
    }

    public function red(): self
    {
        return $this->write($this->driver::CSI . "31m");
    }

    public function green(): self
    {
        return $this->write($this->driver::CSI . "32m");
    }

    public function yellow(): self
    {
        return $this->write($this->driver::CSI . "33m");
    }

    public function blue(): self
    {
        return $this->write($this->driver::CSI . "34m");
    }

    public function magenta(): self
    {
        return $this->write($this->driver::CSI . "35m");
    }

    public function cyan(): self
    {
        return $this->write($this->driver::CSI . "36m");
    }

    public function white(): self
    {
        return $this->write($this->driver::CSI . "37m");
    }

    public function default(): self
    {
        return $this->write($this->driver::CSI . "39m");
    }

    public function bgBlack(): self
    {
        return $this->write($this->driver::CSI . "40m");
    }

    public function bgRed(): self
    {
        return $this->write($this->driver::CSI . "41m");
    }

    public function bgGreen(): self
    {
        return $this->write($this->driver::CSI . "42m");
    }

    public function bgYellow(): self
    {
        return $this->write($this->driver::CSI . "43m");
    }

    public function bgBlue(): self
    {
        return $this->write($this->driver::CSI . "44m");
    }

    public function bgMagenta(): self
    {
        return $this->write($this->driver::CSI . "45m");
    }

    public function bgCyan(): self
    {
        return $this->write($this->driver::CSI . "46m");
    }

    public function bgWhite(): self
    {
        return $this->write($this->driver::CSI . "47m");
    }

    public function bgDefault(): self
    {
        return $this->write($this->driver::CSI . "49m");
    }

    public function brightBlack(): self
    {
        return $this->write($this->driver::CSI . "90m");
    }

    public function brightRed(): self
    {
        return $this->write($this->driver::CSI . "91m");
    }

    public function brightGreen(): self
    {
        return $this->write($this->driver::CSI . "92m");
    }

    public function brightYellow(): self
    {
        return $this->write($this->driver::CSI . "93m");
    }

    public function brightBlue(): self
    {
        return $this->write($this->driver::CSI . "94m");
    }

    public function brightMagenta(): self
    {
        return $this->write($this->driver::CSI . "95m");
    }

    public function brightCyan(): self
    {
        return $this->write($this->driver::CSI . "96m");
    }

    public function brightWhite(): self
    {
        return $this->write($this->driver::CSI . "97m");
    }

    public function brightBgBlack(): self
    {
        return $this->write($this->driver::CSI . "100m");
    }

    public function brightBgRed(): self
    {
        return $this->write($this->driver::CSI . "101m");
    }

    public function brightBgGreen(): self
    {
        return $this->write($this->driver::CSI . "102m");
    }

    public function brightBgYellow(): self
    {
        return $this->write($this->driver::CSI . "103m");
    }

    public function brightBgBlue(): self
    {
        return $this->write($this->driver::CSI . "104m");
    }

    public function brightBgMagenta(): self
    {
        return $this->write($this->driver::CSI . "105m");
    }

    public function brightBgCyan(): self
    {
        return $this->write($this->driver::CSI . "106m");
    }

    public function brightBgWhite(): self
    {
        return $this->write($this->driver::CSI . "107m");
    }

    public function fgi(int $color): self
    {
        return $this->write($this->driver::CSI . "38;5;$color" . 'm');
    }

    public function bgi(int $color): self
    {
        return $this->write($this->driver::CSI . "48;5;$color" . 'm');
    }

    public function rgb(int $red, int $green, int $blue): self
    {
        return $this->write($this->driver::CSI . "38;2;$red;$green;$blue" . 'm');
    }

    public function bgRgb(int $red, int $green, int $blue): self
    {
        return $this->write($this->driver::CSI . "48;2;$red;$green;$blue" . 'm');
    }

    public function moveCursorTo(int $y, int $x): self
    {
        return $this->write($this->driver::CSI . "$y;$x" . 'H');
    }
}