<?php

namespace App\Events;

class SmsEvent
{

    private $params;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $params)
    {
        $this->params = $params;

    }
    public function getParams(): array {
        return $this->params;
    }

}
