<?php

namespace Tests\Unit;

use App\DataSources\HolidaysDumbSource;
use App\Tools\BusinessDatesCalc;
use Tests\TestCase;

class BusinessDaysTest extends TestCase
{
    public function compareResults()
    {

    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testBusinesDatesLogic()
    {
        $bdaysUtil = new BusinessDatesCalc(new HolidaysDumbSource());

        // Playing with different input formats
        $result = $bdaysUtil->calculateBusinessDate(new \DateTime('July 29 2019'), 1);
        $this->assertEquals([
            'holidayDays' => 0,
            'weekendDays' => 0,
            'totalDays' => 1,
            'businessDate' => '2019-07-30T00:00:00Z'
        ], $result);

        // Playing with different input formats
        $result = $bdaysUtil->calculateBusinessDate(new \DateTime('2019-07-29T00:00:00Z'), 1);
        $this->assertEquals([
            'holidayDays' => 0,
            'weekendDays' => 0,
            'totalDays' => 1,
            'businessDate' => '2019-07-30T00:00:00Z'
        ], $result);

        /*
        nov 2018
                   sa su Mo Tu We Th Fr
                   10 11 12 13 14 15 16
        buis.d.           1  2  3
        Holiday        1
        W/E day     1  2
                      ^^^ <- overlap
        2018-11-11 appears to be Veterans day. It overlaps with a w/e day thus and we need to count 3+2 days from Nov, 10.
        According to the task it should be Nov, 15 - all ok
        */
        $result = $bdaysUtil->calculateBusinessDate(new \DateTime('November 10 2018'), 3);
        $this->assertEquals([
            'holidayDays' => 1,
            'weekendDays' => 2,
            'totalDays' => 5,
            'businessDate' => '2018-11-15T00:00:00Z'
        ], $result);


        /*
        nov 2018
                   Th Fr sa su Mo Tu We Th Fr
                   15 16 17 18 19 20
        buis.d.     1  2        3
        Holiday
        W/E day           1  2
        Task says it should be nov, 19 but I see no reason for that. I bet it should be 20.
        */
        $result = $bdaysUtil->calculateBusinessDate(new \DateTime('November 15 2018'), 3);
        $this->assertEquals([
            'holidayDays' => 0,
            'weekendDays' => 2,
            'totalDays' => 5,
            'businessDate' => '2018-11-20T00:00:00Z' # Wrong result in task description
        ], $result);

        /*
                dec 2018             jan 2019
                Tu We Th Fr Sa Su Mo Tu We Th Fr Sa Su Mo Tu We Th Fr Sa Su Mo Tu We Th Fr Sa Su Mo Tu We Th
                25 26 27 28 29 30 31  1  2  3  4  5  6  7  8  9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24
        buis.d.     1  2  3        4     5  6  7        8  9 10 11 12       13 14 15 16 17          18 19 20
        Holiday  1                    2                                                           3
        W/E day              1  2                 3  4                 5  6                 7  8

        The test case provided in the task description is wrong.
        According to the https://www.interstatecapital.com/us-bank-holidays/ mentioned in the task,
        2019-01-21 is a bank holiday Martin Luther King, Jr., Day thus there are 3 holidays - dec 25, jan 1, jan 21.
        Also, no holidays overlap with weekends thus 20 bank days is actually equal to:
        20 + 8 w/e days + 3 hollidays == 31 days delay
        While task says it should be Jan, 18, it is actually Jan, 25
        Asuming there're no holidays neither weekends, just 20 days from dec 25 is Jan 18 :)
        */
        $bdaysUtil = new BusinessDatesCalc(new HolidaysDumbSource());
        $result = $bdaysUtil->calculateBusinessDate(new \DateTime('December 25 2018'), 20);
        $this->assertEquals([
            'holidayDays' => 3, # Wrong result in task description
            'weekendDays' => 8,
            'totalDays' => 31,
            'businessDate' => '2019-01-25T00:00:00Z'  # Wrong result in task description
        ], $result);
    }
}
