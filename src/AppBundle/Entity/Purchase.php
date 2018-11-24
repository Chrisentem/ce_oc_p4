<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;

/**
 * Purchase
 *
 * @ORM\Table(name="purchase")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PurchaseRepository")
 * @AppAssert\NotAvailableTicketType(groups={"step1"}, message="invalid.ticket_type")
 * @AppAssert\NotAvailableTicketNum(groups={"step1"}, message="invalid.ticket_number")
 * @AppAssert\NotSunday(groups={"step1"}, message="invalid.sunday")
 * @AppAssert\NotTuesday(groups={"step1"}, message="invalid.tuesday")
 * @AppAssert\NotPastDate(groups={"step1"}, message="invalid.past_date")
 * @AppAssert\NotHoliday(groups={"step1"}, message="invalid.holidays")
 * @AppAssert\NotInTimeForCurrentDay(groups={"step1"}, message="invalid.too_late_closing")
 */
class Purchase
{
    const STATUS_STEP_1 = 1;
    const STATUS_STEP_2 = 2;
    const STATUS_STEP_3 = 3;
    const STATUS_STEP_4 = 4;

    const MAX_PURCHASE_TICKETS = 6;

    const FULL_DAY_TICKET_TYPE = 0;
    const HALF_DAY_TICKET_TYPE = 1;

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->date = new \Datetime();
      $this->tickets   = new ArrayCollection();
      $this->status = self::STATUS_STEP_1;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\DateTime(groups={"step1"})
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * 
     * @Assert\Email(groups={"step3"})
     */
    private $email;

    /**
     * @var int
     *
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(name="booking_code", type="string", length=255)
     */
    private $bookingCode;

    /**
     * @var bool
     * @Assert\IsTrue(groups={"step3"})
     */
    private $agree;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_visit", type="date")
     * @Assert\Date(groups={"step1"})
     */
    private $dateOfVisit;

    /**
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EntryTicket", mappedBy="purchase", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid(groups={"step2"})
     */
    private $tickets;

    /**
     * @var int
     * @Assert\Range(
     * min = 1,
     * max = Purchase::MAX_PURCHASE_TICKETS,
     * )
     */
    private $numberOfTickets;

    /**
     * @var string
     * @ORM\Column(name="ticket_type", type="string", length=10)
     */
    private $ticketType;

    /**
     * @var int
     */
    private $total;

    /**
     * Get total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }    

    /**
     * Set total
     *
     * @param int $total
     *
     * @return Purchase
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }    

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Purchase
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Purchase
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateOfVisit
     *
     * @param \Datetime $dateOfVisit
     *
     * @return Purchase
     */
    public function setDateOfVisit($dateOfVisit)
    {
        $this->dateOfVisit = $dateOfVisit;

        return $this;
    }

    /**
     * Get dateOfVisit
     *
     * @return \Datetime
     */
    public function getDateOfVisit()
    {
        return $this->dateOfVisit;
    }

    /**
     * Add ticket
     *
     * @param \AppBundle\Entity\EntryTicket $ticket
     *
     * @return Purchase
     */
    public function addTicket($ticket)
    {
        $this->tickets[] = $ticket;
        $ticket->setPurchase($this);

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \AppBundle\Entity\EntryTicket $ticket
     */
    public function removeTicket($ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * Set ticketType
     *
     * @param string $ticketType
     *
     * @return Purchase
     */
    public function setTicketType($ticketType)
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * Get ticketType
     *
     * @return string
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * Get numberOfTickets
     */
    public function getNumberOfTickets()
    {
        return $this->numberOfTickets;
    }

    /**
     * Set numberOfTickets
     * @param $numberOfTickets
     * @return Purchase
     */
    public function setNumberOfTickets($numberOfTickets)
    {
        $this->numberOfTickets = $numberOfTickets;

        return $this;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $agree
     */
    public function setAgree($agree)
    {
        $this->agree = $agree;
    }

    /**
     * @return mixed
     */
    public function getAgree()
    {
        return $this->agree;
    }

    /**
     * @return string
     */
    public function getBookingCode()
    {
        return $this->bookingCode;
    }

    /**
     * @param string $bookingCode
     */
    public function setBookingCode($bookingCode)
    {
        $this->bookingCode = $bookingCode;
    }

}
