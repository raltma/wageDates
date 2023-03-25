<?php 

namespace spinTek\objects;

class PayDate {
    public string $payDate;
    public string $reminderDate;

    function __construct(string $payDate, string $reminderDate)
    {
        $this->payDate = $payDate;
        $this->reminderDate = $reminderDate;
    }
}