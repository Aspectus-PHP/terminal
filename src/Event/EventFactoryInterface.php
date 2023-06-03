<?php

namespace Aspectus\Terminal\Event;

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