<?php

namespace achegedus\UptimeMonitor\Helpers\UptimeResponseCheckers;

use Psr\Http\Message\ResponseInterface;
use achegedus\UptimeMonitor\Models\Monitor;

interface UptimeResponseChecker
{
    public function isValidResponse(ResponseInterface $response, Monitor $monitor) : bool;

    public function getFailureReason(ResponseInterface $response, Monitor $monitor) : string;
}
