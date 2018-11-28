<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\EntryTicket;
use AppBundle\Entity\Purchase;
use AppBundle\Manager\PriceManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PriceManagerTest extends WebTestCase
{
    /**
     * @var EntryTicket
     */
    private $ticket;

    /**
     * @var Purchase
     */
    private $purchase;

    public function setUp()
    {
        $this->ticket = new EntryTicket();
        $this->purchase = new Purchase();
    }


    /**
     * Testing if the right prices are generated
     * @dataProvider priceProvider
     * @param $dateOfBirth
     * @param $discount
     * @param $ticketType
     * @param $visitDate
     * @param $expectedPrice
     */
    public function testComputeTicketPrice($dateOfBirth, $discount, $ticketType, $visitDate, $expectedPrice)
    {
        $this->ticket->setBirthdate($dateOfBirth);
        $this->ticket->setDiscounted($discount);
        $this->purchase->setDateOfVisit($visitDate);
        $this->purchase->setTicketType($ticketType);
        $this->purchase->addTicket($this->ticket);

        $priceManager = new PriceManager();
        $result = $priceManager->computeTicketPrice($this->ticket);
        $this->assertEquals($expectedPrice, $result);
    }

    /**
     * @return array
     */
    public function priceProvider()
    {
        return [
            [new \DateTime('2015-09-21'), 0, 1, new \DateTime('2018-11-26'), 0],
            [new \DateTime('1977-10-02'), 1, 0, new \DateTime('2018-11-26'), 10],
            [new \DateTime('1977-10-02'), 1, 1, new \DateTime('2018-11-26'), 5],
            [new \DateTime('2000-09-21'), 0, 1, new \DateTime('2018-11-28'), 8],
            [new \DateTime('2000-09-21'), 0, 0, new \DateTime('2018-11-28'), 16],
            [new \DateTime('1951-09-21'), 0, 0, new \DateTime('2018-11-28'), 12],
            [new \DateTime('1951-09-21'), 0, 1, new \DateTime('2018-11-28'), 6],


        ];
    }
}
