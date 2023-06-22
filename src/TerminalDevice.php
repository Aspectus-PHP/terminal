<?php

namespace Aspectus\Terminal;

use Amp\ByteStream\ClosedException;
use Amp\ByteStream\ReadableResourceStream;
use Amp\ByteStream\ReadableStream;
use Amp\ByteStream\StreamException;
use Amp\ByteStream\WritableResourceStream;
use Amp\ByteStream\WritableStream;
use Amp\DeferredCancellation;
use Amp\Future;
use Aspectus\Terminal\Event\EscapeSequenceEvent;
use Aspectus\Terminal\Event\EventFactoryInterface;
use Aspectus\Terminal\Event\InputEvent;
use function Amp\async;

/**
 * This is a gateway abstraction for the terminal using non-blocking read/write interfaces of amphp/byte-stream.
 */
final class TerminalDevice
{
    private readonly WritableStream $output;
    private readonly ReadableStream $input;
    private readonly WritableStream $errorStream;

    private array $listeners = [];
    private ?Future $readingFuture = null;
    private ?DeferredCancellation $readCancellation;

    /**
     * @param ReadableStream|resource $input
     * @param WritableStream|resource $output
     * @param WritableStream|resource $error
     */
    public function __construct(
        $input = \STDIN,
        $output = \STDOUT,
        $error = \STDERR,
        private ?EventFactoryInterface $eventFactory = null,
        private ?ExceptionHandlerInterface $exceptionHandler = null
    ) {
        $this->input = is_resource($input) ? new ReadableResourceStream($input) : $input;
        $this->output = is_resource($output) ? new WritableResourceStream($output) : $output;
        $this->errorStream = is_resource($error) ? new WritableResourceStream($error) : $error;
    }

    /**
     * Writes to the output stream
     *
     * @param string $data
     * @return void
     * @throws ClosedException|StreamException
     */
    public function write(string $data): void
    {
        $this->output->write($data);
    }

    /**
     * Writes to the error stream
     *
     * @param string $data
     * @return void
     * @throws ClosedException|StreamException
     */
    public function error(string $data): void
    {
        $this->errorStream->write($data);
    }

    /**
     * Subscribes a listener to a specific event
     *
     * @template E of InputEvent
     * @template C of callable(E):?true|\Closure(E):?true
     *
     * @param class-string<E> $event
     * @param C $listener   Return `true` to unsubscribe
     * @return void
     */
    public function subscribe(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;

        if (!$this->readingFuture) {
            $this->readingFuture = async($this->read(...));
        }
    }

    private function read(): void
    {
        try {
            $this->readCancellation = new DeferredCancellation();

            while ('' !== ($chunk = $this->input->read($this->readCancellation->getCancellation()))) {
                if ($chunk) {
                    $this->processChunk($chunk);
                }
            }
        } catch (\Throwable $exception) {
            $this->exceptionHandler?->onException($exception);
        }
    }

    private function processChunk(string $data): void
    {
        // Allows swapping the event factory on runtime
        if ($this->eventFactory) {
            if ($event = $this->eventFactory->createEvent($data)) {
                $this->doDispatch($event);
            }

            // if an event factory is present, we have fully handed control
            return;
        }

        if (str_starts_with($data, "\e") && strlen($data) > 1) {
            $event = EscapeSequenceEvent::fromString($data);
        }

        if (!isset($event)) {
            $event = InputEvent::fromString($data);
        }

        $this->doDispatch($event);
    }

    private function doDispatch(InputEvent $event): void
    {
        if (!isset($this->listeners[$event::class])) {
            return;
        }

        $event->device = $this;

        foreach ($this->listeners[$event::class] as $key => $listener) {
            if ($listener($event)) {
                unset($this->listeners[$event::class][$key]);

                if (count($this->listeners) === 0) {
                    $this->readCancellation?->cancel();
                }
            }
        }
    }

    public function free(): void
    {
        $this->input->close();
        $this->output->close();
        $this->errorStream->close();
    }

    public function setEventFactory(?EventFactoryInterface $eventFactory): void
    {
        $this->eventFactory = $eventFactory;
    }
}
