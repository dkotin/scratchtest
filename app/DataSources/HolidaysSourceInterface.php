<?php


namespace App\DataSources;


interface HolidaysSourceInterface
{
    /**
     * @param \DateTime $date
     * @return bool
     */
    public function isHoliday(\DateTime $date): bool;
}
