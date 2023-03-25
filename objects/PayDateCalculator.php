<?php
namespace spinTek\objects;

require_once "./objects/PayDate.php";
require_once "./enums/Direction.php";
use DateTime;
use spinTek\objects\PayDate;
use spinTek\enums\Direction;

class PayDateCalculator
{
    private int $year;
    private string $format = "Y-m-d";
    private array $nationalHolidays = [];
    public array $payDates = [];

    function __construct(?int $year)
    {
        $year = $year ?? date("Y");
        $this->year = $year;
        $this->initializeNationalHolidays();
    }

    public function calculatePayDates(int $remindDelayDays = 3): array
    {
        $this->payDates = [];
        for ($i = 1; $i <= 12; $i++) {
            // Create initial pay date and correct it.
            $month = str_pad($i, 2, "0", STR_PAD_LEFT);
            $initialPayDate = "{$this->year}-$month-10";
            $payDate = $this->correctDate($initialPayDate);
            // Create intial reminder date and correct it
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

    private function correctDate(string $payDate, Direction $direction = Direction::Forward): string
    {
        // If the current date is excluded or its weekend then move 1 day in specified direction and recurse this function
        if (in_array($payDate, $this->nationalHolidays) || $this->isWeekend($payDate)) {
            $newPayDate = new DateTime($payDate);
            $newPayDate = $newPayDate->modify($direction->value)
                ->format($this->format);
            $payDate = $this->correctDate($newPayDate, $direction);
        }
        return $payDate;
    }

    public function getNationalHolidaysJson(): string
    {
        return json_encode($this->nationalHolidays);
    }

    private function initializeNationalHolidays(): void
    {
        $this->nationalHolidays = [];
        $this->nationalHolidays = array_merge(
            $this->getMovingHolidays(),
            $this->getStaticHolidays()
        );
        sort($this->nationalHolidays, SORT_STRING);
    }
    private function getStaticHolidays(): array
    {
        $staticHolidays = [
            // New Years Eve
            "{$this->year}-01-01",
            // Independence Day
            "{$this->year}-02-24",
            // Spring Day
            "{$this->year}-05-01",
            // Victory Day
            "{$this->year}-06-23",
            // Midsummer Day
            "{$this->year}-06-24",
            // Independence Restoration Day
            "{$this->year}-08-20",
            // Christmas Eve
            "{$this->year}-12-24",
            // First Christmas Day
            "{$this->year}-12-25",
            // Second Christmas Day
            "{$this->year}-12-26", 
        ];
        return $staticHolidays;
    }
    private function getMovingHolidays(): array
    {
        $easter = $this->getEasterDate();
        $easterDateTime = new DateTime($easter);

        $goodFriday = $easterDateTime->modify("-2 days");
        $goodFriday = $goodFriday->format($this->format);

        $pentecost = $easterDateTime->modify("+51 days");
        $pentecost = $pentecost->format($this->format);


        return [$easter, $goodFriday, $pentecost];
    }

    private function getEasterDate(): string
    {
        /* https://en.wikipedia.org/wiki/Date_of_Easter
        Gauss's Easter algorithm */
        $a = $this->year % 19;
        $b = $this->year % 4;
        $c = $this->year % 7;
        $p = floor($this->year / 100);
        $q = floor((int) (13 + 8 * $p) / 25);
        $m = (int) (15 - $q + $p - ($p / 4)) % 30;
        $n = (int) (4 + $p - ($p / 4)) % 7;
        $d = (19 * $a + $m) % 30;
        $e = ($n + 2 * $b + 4 * $c + 6 * $d) % 7;

        $daysToAdd = $d + $e + 22;

        if ($d === 28 && $e === 6)
            return "{$this->year}-04-18"; // Special case
        if ($d === 29 && $e === 6)
            return "{$this->year}-04-19"; // Special case
        if ($daysToAdd > 31)
            return "{$this->year}-04-" . str_pad($daysToAdd - 31, 2, "0", STR_PAD_LEFT);
        else
            return "{$this->year}-03-" . str_pad($daysToAdd, 2, "0", STR_PAD_LEFT);
    }

    private function isWeekend(string $date): string
    {
        return (date('N', strtotime($date)) >= 6);
    }
}