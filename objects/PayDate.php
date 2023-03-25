<?php 

namespace spinTek\objects;

use DateTime;

class PayDate {
    public string $payDate;
    public string $reminderDate;

    function __construct(string $payDate, $reminderDate)
    {
        $this->payDate = $payDate;
        $this->reminderDate = $reminderDate;
    }


}