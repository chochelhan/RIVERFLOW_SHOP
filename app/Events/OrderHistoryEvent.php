<?php

namespace App\Events;

class OrderHistoryEvent
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
