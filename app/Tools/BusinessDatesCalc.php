<?php


namespace App\Tools;

use App\DataSources\HolidaysSourceInterface;


class BusinessDatesCalc implements BusinessDatesCalcInterface
{

    CONST WEEKEND_DAYS = [6, 7]; // week starts on monday and that's the day #1
    private $holidaysDataProvider;

    public function __construct(HolidaysSourceInterface $holidaysProvider)
    {
        $this->holidaysDataProvider = $holidaysProvider;
    }

    /**
     * @param \DateTime $date
     * @param int $delay
     * @return array
     * @throws \Exception
     */
    public function calculateBusinessDate(\DateTime $date, int $delay)
    {
        $resultDate = clone $date;
        $segment = $delay;
        $holidaysCount = 0;
        $weekendDaysCount = 0;
        $offDaysCount = 0;
        $interval = new \DateInterval("P1D");

        while ($segment > 0) {
            $isHoliday = $this->isHoliday($resultDate);
            $isWeekendDay = $this->isWeekendDay($resultDate);
            if ($isHoliday || $isWeekendDay) {
                $segment++;

                if ($isHoliday) {
                    $holidaysCount++;
                }

                if ($isWeekendDay) {
                    $weekendDaysCount++;
                }

                $offDaysCount++;
            }

            $segment--;
            $resultDate->add($interval);
        }
        $totalDaysCount = $delay + $offDaysCount;
        $addDays = $totalDaysCount; # > 0 ? $totalDaysCount - 1 : $totalDaysCount;

        $resultDate = clone $date;
        $resultDate->add(new \DateInterval("P{$addDays}D"));

        $results = [];
        $results['holidayDays'] = $holidaysCount;
        $results['weekendDays'] = $weekendDaysCount;
        $results['totalDays'] = $totalDaysCount;
        $results['businessDate'] = $this->getFormattedDate($resultDate);

        return $results;
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function isHoliday(\DateTime $date)
    {
        return $this->holidaysDataProvider->isHoliday($date);
    }

    /**
     * @param \DateTime $date
     * @param int $days
     * @return int
     * @throws \Exception
     */
    public function isWeekendDay(\DateTime $date)
    {
        if (in_array($date->format('N'), self::WEEKEND_DAYS)) {
            return true;
        }

        return false;
    }

    /**
     * @param \DateTime $date
     * @return string
     */
    private function getFormattedDate(\DateTime $date)
    {
        // A dirty hack for Z-timezone instead of + 0000 - PHP has no format specifier for the Z-zone
        return str_replace('+0000', 'Z', $date->format('Y-m-d\TH:i:sO'));
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function isBusinessDay(\DateTime $date)
    {
        if (!$this->isWeekendDay($date) || $this->isHoliday($date)) {
            return false;
        }

        return true;
    }

    /**
     * @param $success
     * @param $request
     * @param $response
     * @return mixed
     */
    public function prepareResponse($success, $request, $response, $error = null)
    {
        $result = [
            'ok' => $success,
            'initialQuery' => $request,
        ];

        if ($response) {
            $result['response'] = $response;
        }

        if ($error) {
            $result['error'] = $error;
        }

        return $result;
    }
}

