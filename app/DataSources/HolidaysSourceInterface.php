<?php


namespace App\DataSources;


interface HolidaysSourceInterface
{
    public function __construct($country = null);
    public function isHoliday(\DateTime $date);
}
