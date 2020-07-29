<?php

namespace App\Events;

class PayReportEvent extends Event
{
    public $type = "";
    public $pay_report;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($type, $pay_report)
    {
        $this->type = $type;
        $this->pay_report = $pay_report;
    }
}
