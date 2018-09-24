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
 */
class Purchase
{

    const STATUS_0 = "initialized";
    const STATUS_1 = "confirmed";
    const STATUS_2 = "paid";
    const STATUS_3 = "sent";

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->date = new \Datetime();
      $this->tickets   = new ArrayCollection();
      $this->email = "";
      $this->status = self::STATUS_0;
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
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \Date
     *
     * @ORM\Column(name="date_visit", type="date")
     * @Assert\Date()
     * @appAssert\Dateavailable()
     */
    private $dateOfVisit;

    /**
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EntryTicket", mappedBy="Purchase")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $tickets;

    /**
     * @var int
     * @Assert\Range(
     * min = 1,
     * max = 6,
     * )
     */
    private $numberOfTickets;

    /**
     * @var string
     * @ORM\Column(name="ticket_type", type="string", length=10)
     * @AppAssert\Tickettypeavailable()
     */
    private $ticketType;


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
     * Set status
     *
     * @param string $status
     *
     * @return Purchase
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateOfVisit
     *
     * @param \Date $dateOfVisit
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
     * @return \Date
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
    public function addTicket(\AppBundle\Entity\EntryTicket $ticket)
    {
        $this->tickets[] = $ticket;

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \AppBundle\Entity\EntryTicket $ticket
     */
    public function removeTicket(\AppBundle\Entity\EntryTicket $ticket)
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
    public function getnumberOfTickets()
    {
        return $this->numberOfTickets;
    }

    /**
     * Set numberOfTickets
     */
    public function setnumberOfTickets($numberOfTickets)
    {
        $this->numberOfTickets = $numberOfTickets;

        return $this;
    }
}
