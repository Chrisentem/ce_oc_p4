<?php

namespace Tests\AppBundle\Validator\Constraints;


use AppBundle\Entity\Purchase;
use AppBundle\Service\Time;
use AppBundle\Validator\Constraints\NotPastDate;
use AppBundle\Validator\Constraints\NotPastDateValidator;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * Class NotPastDateValidatorTest.
 */
class NotPastDateValidatorTest extends ValidatorTestAbstract
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
        return new NotPastDateValidator();
    }

    /**
     * Testing if DateOfVisit is not past
     * @dataProvider dateProviderOk
     * @param $date
     */
    public function testValidationOk($date)
    {
        // We set the "fake" time with timeTravel method
        $this->timeTravel('2018-11-26 00:00:00');
        $notPastDateConstraint = new NotPastDate();
        $notPastDateValidator = $this->initValidator();

        $this->purchase->setDateOfVisit($date);
        $notPastDateValidator->validate($this->purchase, $notPastDateConstraint);
    }

    /**
     * @return array
     */
    public function dateProviderOk()
    {
        return [
            [new \Datetime('2019-01-09')],
            [new \Datetime('2019-02-15')],
            [new \Datetime('2019-03-18')],
        ];
    }

    /**
     * Testing if DateOfVisit is past
     * @dataProvider dateProviderKo
     * @param $date
     */
    public function testValidationKo($date)
    {
        // We set the "fake" time with timeTravel method
        $this->timeTravel('2018-11-26 00:00:00');
        $notPastDateConstraint = new NotPastDate();
        $notPastDateValidator = $this->initValidator($notPastDateConstraint->message);

        $this->purchase->setDateOfVisit($date);
        $notPastDateValidator->validate($this->purchase, $notPastDateConstraint);

    }

    /**
     * @return array
     */
    public function dateProviderKo()
    {
        return [
            [new \Datetime('2018-01-06')],
            [new \Datetime('2018-02-17')],
            [new \Datetime('2018-04-21')],
        ];
    }
}