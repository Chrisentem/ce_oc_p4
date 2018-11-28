<?php

namespace Tests\AppBundle\Validator\Constraints;


use AppBundle\Entity\Purchase;
use AppBundle\Repository\PurchaseRepository;
use AppBundle\Validator\Constraints\NotAvailableTicketNum;
use AppBundle\Validator\Constraints\NotAvailableTicketNumValidator;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class NotAvailableTicketNumValidatorTest.
 */
class NotAvailableTicketNumValidatorTest extends ValidatorTestAbstract
{
    /**
     * @var Purchase
     */
    private $purchase;

    /**
     * @var MockObject
     */
    private $em;

    /**
     * @var MockObject
     */
    private $purchaseRepository;

    /**
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->purchase = new Purchase();
        // Now, mock the repository so it returns the mock of the purchase repo
        $this->purchaseRepository = $this->createMock(PurchaseRepository::class);
        // Last, mock the EntityManager to return the mock of the repository
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])  // We indicates that a method will be defined
            ->getMock();
        $this->em->method('getRepository')->willReturn($this->purchaseRepository);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidatorInstance()
    {
        return new NotAvailableTicketNumValidator($this->em);
    }

    /**
     * Testing if maximum ticket number available for purchase per day is not reached
     * @dataProvider dateProviderOk
     * @param $date
     * @param $numberOfTickets
     * @param $totalDayTicketsSold
     */
    public function testValidationOk($date, $numberOfTickets, $totalDayTicketsSold)
    {
        $this->purchaseRepository->expects($this->any())
            ->method('ticketsSoldOnChosenDate')
            ->willReturn($totalDayTicketsSold);

        $notAvailableTicketNumConstraint = new NotAvailableTicketNum();
        $notAvailableTicketNumValidator = $this->initValidator();

        $this->purchase->setDateOfVisit($date);
        $this->purchase->setNumberOfTickets($numberOfTickets);
        $notAvailableTicketNumValidator->validate($this->purchase, $notAvailableTicketNumConstraint);
    }

    /**
     * @return array
     */
    public function dateProviderOk()
    {
        return [
            [new \Datetime('2019-01-09'), 5, 500],
        ];
    }

    /**
     * Testing if maximum ticket number available for purchase per day is reached
     * @dataProvider dateProviderKo
     * @param $date
     * @param $numberOfTickets
     * @param $totalDayTicketsSold
     */
    public function testValidationKo($date, $numberOfTickets, $totalDayTicketsSold)
    {
        $this->purchaseRepository->expects($this->any())
            ->method('ticketsSoldOnChosenDate')
            ->willReturn($totalDayTicketsSold);

        $notAvailableTicketNumConstraint = new NotAvailableTicketNum();
        $notAvailableTicketNumValidator = $this->initValidator($notAvailableTicketNumConstraint->message);

        $this->purchase->setDateOfVisit($date);
        $this->purchase->setNumberOfTickets($numberOfTickets);
        $notAvailableTicketNumValidator->validate($this->purchase, $notAvailableTicketNumConstraint);

    }

    /**
     * @return array
     */
    public function dateProviderKo()
    {
        return [
            [new \Datetime('2019-01-09'), 5, 996],
            [new \Datetime('2018-02-17'), 6, 999],
            [new \Datetime('2018-04-21'), 5, 1000],
        ];
    }
}