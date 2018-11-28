<?php

namespace Tests\AppBundle\Validator\Constraints;


use AppBundle\Service\Time;
use AppBundle\Entity\Purchase;
use AppBundle\Validator\Constraints\NotAvailableTicketType;
use AppBundle\Validator\Constraints\NotAvailableTicketTypeValidator;
use Symfony\Bridge\PhpUnit\ClockMock;


/**
 * Class NotAvailableTicketTypeValidatorTest.
 * @group time-sensitive
 */
class NotAvailableTicketTypeValidatorTest extends ValidatorTestAbstract
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
        return new NotAvailableTicketTypeValidator();
    }

    /**
     * Testing if ticket type is available
     * @dataProvider dateProviderOk
     * @param \DateTime $date
     * @param int $type integer 0 or 1
     */
    public function testValidationOk($date, $type)
    {
        // We set the "fake" time with timeTravel method
        $this->timeTravel($date);
        $notAvailableTicketTypeConstraint = new NotAvailableTicketType();
        $notAvailableTicketTypeValidator = $this->initValidator();
        // We need DateTime object to pass in Purchase setters
        $date = new \DateTime($date);
        $this->purchase->setDateOfVisit($date);
        $this->purchase->setTicketType($type);
        $notAvailableTicketTypeValidator->validate($this->purchase, $notAvailableTicketTypeConstraint);

    }

    /**
     * @return array
     */
    public function dateProviderOk()
    {
        // Here we use string values to use with timeTravel method
        return [
            // A day with full-day ticket purchased before 14pm
            ['2018-11-12 09:00:00', 0],
            // A day with half-day ticket purchased after 14pm
            ['2018-11-12 15:00:00', 1],
        ];
    }

    /**
     * Testing if ticket type is not available
     * @dataProvider dateProviderKo
     * @param \DateTime $date
     * @param int $type
     */
    public function testValidationKo($date, $type)
    {
        // We set the "fake" time with timeTravel method
        $this->timeTravel($date);
        $notAvailableTicketTypeConstraint = new NotAvailableTicketType();
        $notAvailableTicketTypeValidator = $this->initValidator($notAvailableTicketTypeConstraint->message);
        // We need DateTime object to pass in Purchase setters
        $date = new \DateTime($date);
        $this->purchase->setDateOfVisit($date);
        $this->purchase->setTicketType($type);
        $notAvailableTicketTypeValidator->validate($this->purchase, $notAvailableTicketTypeConstraint);

    }

    /**
     * @return array
     */
    public function dateProviderKo()
    {
        // Here we use string values to use with timeTravel method
        return [
            // All purchase after 14pm
            ['2018-11-12 16:00:00', 0],
            ['2018-10-12 14:10:00', 0],
            ['2017-01-12 14:00:00', 0],
        ];
    }
}