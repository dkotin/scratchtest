<?php

namespace App\Tools;

interface BusinessDatesCalcInterface
{
    public function calculateBusinessDate(\DateTime $date, int $delay);

    public function prepareResponse($success, $request, $response, $error = null);

    public function isBusinessDay(\DateTime $date);

    public function isWeekendDay(\DateTime $date);
}
