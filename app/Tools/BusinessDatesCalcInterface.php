<?php

namespace App\Tools;

interface BusinessDatesCalcInterface
{
    /**
     * @param \DateTime $date
     * @param int $delay
     * @return array
     * @throws \Exception
     */
    public function calculateBusinessDate(\DateTime $date, int $delay): array;

    /**
     * @param $success
     * @param $request
     * @param $response
     * @return array
     */
    public function prepareResponse(bool $success, array $request, ?array $response, $error = null): array;

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function isBusinessDay(\DateTime $date): bool;

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function isWeekendDay(\DateTime $date): bool;
}
