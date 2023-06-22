<?php

namespace Aspectus\Terminal;

interface ExceptionHandlerInterface
{
    public function onException(\Throwable $exception): void;
}