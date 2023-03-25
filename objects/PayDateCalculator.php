<?php
namespace spinTek\objects;

require_once "./objects/PayDate.php";
use DateTime;
use spinTek\objects\PayDate;

enum Direction:string{
    case Forward = "+1 days";
    case Backward = "-1 days";
}
class PayDateCalculator{
    private int $year;
    private $format = "Y-m-d";
    private $nationalHolidays = [];
    public $payDates = [];

    function __construct(int $year = 0)
    {
        if($year === 0) $year = date("Y");
        $this->year = $year;
        $this->initializeNationalHolidays();
    }

    public function calculatePayDates($remindDelayDays = 3){
        $this->payDates = [];
        for($i=1;$i<=12;$i++){
            //Create initial pay date and correct it.
            $month = str_pad($i,2,"0",STR_PAD_LEFT);
            $initialPayDate = "{$this->year}-$month-10";
            $payDate = $this->correctDate($initialPayDate);

            //Create intial reminder date and correct it
            $initialReminderDate = new DateTime($payDate);
            $initialReminderDate = $initialReminderDate->modify("-$remindDelayDays days")->format($this->format);
            /* It was not mentioned in the task that reminder date should also exclude weekends and holidays, 
            but here i decided to still exclude these dates, this can be removed easily by just changing the line below to
            $this->reminderDate = $initialRemainderDate */
            $reminderDate = $this->correctDate($initialReminderDate, Direction::Backward);

            $date = new PayDate($payDate, $reminderDate);
            $this->payDates[] = $date;
            }
        return $this->payDates;
    }

    public function correctDate($payDate, Direction $direction = Direction::Forward):string{
        //if the current date is excluded or its weekend then move 1 day in specified direction and recurse this function
        if(in_array($payDate, $this->nationalHolidays) || $this->isWeekend($payDate)){
            $newPayDate = new DateTime($payDate);
            $newPayDate = $newPayDate->modify($direction->value)
                ->format($this->format);
            $payDate = $this->correctDate($newPayDate, $direction);
        }
        return $payDate;
    }

    public function getNationalHolidaysJson(){
        return json_encode($this->nationalHolidays);
    }

    private function initializeNationalHolidays(){
        $this->nationalHolidays = [];
        $this->nationalHolidays = array_merge(
            $this->getMovingHolidays(), 
            $this->getStaticHolidays()
        );
        sort($this->nationalHolidays, SORT_STRING);
    }
    public function getStaticHolidays(){
        $staticHolidays = [
            "{$this->year}-01-01",//Uusaasta
            "{$this->year}-02-24",//Iseseisvuspäev
            "{$this->year}-05-01",//Kevadpüha
            "{$this->year}-06-23",//Võidupüha
            "{$this->year}-06-24",//Jaanipäev
            "{$this->year}-08-20",//Taasiseseisvumispäev
            "{$this->year}-12-24",//Jõululaupäev
            "{$this->year}-12-25",//1.Jõulupüha
            "{$this->year}-12-26",//2.Jõulupüha
        ];
        return $staticHolidays;
    }
    public function getMovingHolidays(){
        $easter = $this->getEasterDate();
        $easterDateTime = new DateTime($easter);

        $goodFriday = $easterDateTime->modify("-2 days");
        $goodFriday = $goodFriday->format($this->format);

        $pentecost = $easterDateTime->modify("+51 days");
        $pentecost = $pentecost->format($this->format);


        return[$easter,$goodFriday,$pentecost];
    }

    public function getEasterDate(){
        //https://en.wikipedia.org/wiki/Date_of_Easter
        //Gauss's Easter algorithm
        $a = $this->year % 19;
        $b = $this->year % 4;
        $c = $this->year % 7;
        $p = floor($this->year / 100);
        $q = floor((int)(13 + 8 * $p) / 25);
        $m = (int)(15 - $q + $p - ($p / 4)) % 30;
        $n = (int)(4 + $p - ($p / 4)) % 7;
        $d = (19 * $a + $m) % 30;
        $e = ($n + 2*$b + 4*$c + 6*$d) % 7;

        $daysToAdd = $d + $e + 22;

        if($d === 28 && $e === 6)   return "{$this->year}-04-18";//special case
        if($d === 29 && $e === 6)   return "{$this->year}-04-19";//special case
        if($daysToAdd > 31)         return "{$this->year}-04-".str_pad($daysToAdd-31, 2, "0", STR_PAD_LEFT);
        else                        return "{$this->year}-03-" . str_pad($daysToAdd, 2, "0",STR_PAD_LEFT);
    }

    public function isWeekend($date) {
        return (date('N', strtotime($date)) >= 6);
    }
}
?>