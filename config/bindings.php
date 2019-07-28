<?php

use Illuminate\Container\Container;

$container = Container::getInstance();

$container->bind(\App\DataSources\HolidaysSourceInterface::class, \App\DataSources\HolidaysDumbSource::class);
$container->bind(\App\Tools\BusinessDatesCalcInterface::class, \App\Tools\BusinessDatesCalc::class);


