<?php

namespace Aspectus\Terminal\Value;

use Aspectus\Terminal\Xterm\ControlDriver\Xterm7BitControlDriver;

class TerminalParameters
{
    public array $parameters;

    public function __construct(string ...$parameters)
    {
        $this->parameters = $parameters;
    }

    public static function fromString(string $string, string $csi = Xterm7BitControlDriver::CSI): self
    {
        $parsed = [];

        $string = str_replace([$csi . '>', 'x'], '', $string);

        $result = strtok($string, ';');
        if (!$result) {
            throw new \Exception('Unable to parse');        // do not throw, missing constructor here?
        }
        $parsed[] = $result;

        while (false !== ($result = strtok(';'))) {
            $parsed[] = $result;
        }

        return new self(...$parsed);
    }
}
