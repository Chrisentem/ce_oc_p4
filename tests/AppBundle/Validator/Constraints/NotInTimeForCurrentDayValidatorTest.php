<?php

namespace Tests\AppBundle\Validator\Constraints;


use AppBundle\Entity\Purchase;
use AppBundle\Service\Time;
use AppBundle\Validator\Constraints\NotInTimeForCurrentDay;
use AppBundle\Validator\Constraints\NotInTimeForCurrentDayValidator;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * Class NotInTimeForCurrentDayValidatorTest.
 */
class NotInTimeForCurrentDayValidatorTest extends ValidatorTestAbstract
{
    /**
     * @var Purchase
     */
    private $purchase;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        // We use the ClockMock on Time class which is used on both sides of the test:
        // the test class and the tested class
        ClockMock::register(Time::class);
    }

    /**
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->purchase = new Purchase();
    }

    /**
     * @param string $time
     */
    protected function timeTravel(string $time)
    {
        ClockMock::withClockMock(strtotime($time));
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidatorInstance()
    {
        return new NotInTimeForCurrentDayValidator();
    }

    /**
     * Testing if the daily time limit to purchase tickets is not reached
     * @dataProvider dateProviderOk
     * @param $date
     */
    public function testValidationOk($date)
    {
        // We set the "fake" time with timeTravel method
        $this->timeTravel($date);
        $notInTimeForCurrentDayConstraint = new NotInTimeForCurrentDay();
        $notInTimeForCurrentDayValidator = $this->initValidator();

        $date = new \Datetime($date);
        $this->purchase->setDateOfVisit($date);
        $notInTimeForCurrentDayValidator->validate($this->purchase, $notInTimeForCurrentDayConstraint);
    }

    /**
     * @return array
     */
    public function dateProviderOk()
    {
        return [
            // Monday, Thursday & Saturday
            ['2018-11-15 09:15:00'],
            ['2018-11-24 16:15:00'],
            ['2018-11-26 12:30:00'],
            // Wednesday & Friday
            ['2018-11-07 18:30:00'],
            ['2018-11-09 19:30:00'],

        ];
    }

    /**
     * Testing if the daily time limit to purchase tickets is reached
     * @dataProvider dateProviderKo
     * @param $date
     */
    public function testValidationKo($date)
    {
        // We set the "fake" time with timeTravel method
        $this->timeTravel($date);
        $notInTimeForCurrentDayConstraint = new NotInTimeForCurrentDay();
        $notInTimeForCurrentDayValidator = $this->initValidator($notInTimeForCurrentDayConstraint->message);

        $date = new \Datetime($date);
        $this->purchase->setDateOfVisit($date);
        $notInTimeForCurrentDayValidator->validate($this->purchase, $notInTimeForCurrentDayConstraint);

    }

    /**
     * @return array
     */
    public function dateProviderKo()
    {
        return [
            // Monday, Thursday & Saturday
            ['2018-11-15 17:15:00'],
            ['2018-11-24 19:15:00'],
            ['2018-11-26 18:00:00'],
            // Wednesday & Friday
            ['2018-11-07 20:50:00'],
            ['2018-11-09 21:15:00'],
        ];
    }
}