<?php


namespace App\DataSources;

// @todo: support for different countries
// @todo: provide a better holidays list based on an online source and cache or something
class HolidaysDumbSource implements HolidaysSourceInterface
{
    private $country;

    public function __construct($country = null)
    {
        $this->country = $country;
    }

    /**
     * @inheritDoc
     */
    public function isHoliday(\DateTime $date): bool
    {
        $holidaysList = $this->getHolidaysList();
        if (in_array($date->format('Y-m-d'), $holidaysList)) {
            return true;
        }

        return false;
    }

    private function getHolidaysList()
    {
        return [
            '2018-11-11',
            '2018-11-22',
            '2018-12-25',
            '2019-01-01',
            '2019-01-21',
            '2019-02-18',
            '2019-05-27',
            '2019-07-04',
            '2019-09-02',
            '2019-10-14',
            '2019-11-11',
            '2019-11-28',
            '2019-12-25',
        ];
    }
}
