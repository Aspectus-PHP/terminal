## Terminal device

This is an open-source PHP package providing a small abstraction for the terminal. The idea is to
see the terminal as a device, which can perform both input and output. The package
relies on Amphp to provide non-blocking handling of the streams and an event dispatcher 
to handle input.

Furthermore, this package also provides an abstraction for Xterm that is using the terminal device,
supporting most of the escape control sequences in the manual (patch #379) as well dispatching events
for handling mouse movement.

## Usage

### Basic Output

```php
use Aspectus\Terminal\TerminalDevice;

$device = new TerminalDevice();

$device->write("This and that");    // will output "This and that" in STDOUT
$device->error("There was some error");     // will output in STDERR
```

### Basic Input

For handling the input you can rely on the supplied EventDispatcher. 

```php

$device->subscribe(
    InputEvent::class,
    function (InputEvent $event) {
        $event->device->write('Read: ' . bin2hex($event->data) . PHP_EOL);
    }
);

$device->subscribe(
    InputEvent::class,
    function (InputEvent $event) {
        if ($event->data === 'q') {
            exit();
        }
    }
);
```

### Device Input Events

The current implementation will distinguish between normal input events (InputEvent)
and input that is an escape sequence (EscapeSequenceEvent), so your listeners can
attach to the correct one. Unless you need to read some escape sequence that the
terminal sends back to you, you probably need to listen to InputEvent.

`InputEvent` - the default event that is emitted.

`EscapeSequenceEvent` - this is emitted when input starts with an escape sequence

All events hold a reference to the device that dispatched them into the `$event->device` property.

For `Xterm events` see below.

### Event Factory

The device by default has a minimal implementation for creating new events. You
can supply your own by implementing the `EventFactoryInterface`.

```php
interface EventFactoryInterface
{
    /**
     * Optionally creates an event to be dispatched
     *
     * @param string $received
     * @return InputEvent|null
     */
    public function createEvent(string $received): ?InputEvent;
}
```

### Xterm abstraction

The package comes with an abstraction for Xterm that provides named methods to escape sequences in
a fluent interface. `Xterm` will buffer every call and write it to the device when `flush()` is called
or return it as a `string` when `getBuffered()` is called.

```php
$xterm = new \Aspectus\Terminal\Xterm();

$xterm
    // we reset colors in the beginning
    ->default()
    ->bgDefault()
    ->normal()
    ->eraseDisplay()

    // position for first message
    ->moveCursorTo(5,5)

    // set style
    ->red()
    ->bgWhite()

    // write a message
    ->write('Hello world!')

    // position for second message
    ->moveCursorTo(8, 10)

    // set style
    ->bold()
    ->brightYellow()
    ->bgBlue()

    // return instructions
    ->write('This is an Xterm abstraction!')

    // write the whole buffer
    ->flush()
```

### Xterm Input events

Using the Xterm abstraction will add a different `EventFactory` implementation which will emit some
additional events when they are received.

`SpecialKeyEvent` is emitted when function or arrow keys are pressed. The `data` property of
the event is mapped to a generic format, indicating function keys like `<F2>` and arrow keys
like `<LEFT>`, `<RIGHT>`.

`MouseInputEvent` - is emitted when mouse tracking is enabled and provides properties like `x` and
`y` as well as methods for the buttons (like `button1()`) to get information about which buttons
were pressed.

`MouseFocusEvent` - is emitted when mouse tracking and focus tracking have been enabled and provides
a way to attach to an event that will trigger when the window loses focus or gains it again.

### More usage examples

More usage examples can be found in the `examples/` directory.


## Contribute

Everyone is welcome to contribute, please see `CONTRIBUTE.md` for more information.

## Licence

MIT