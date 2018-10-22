<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class NotAvailableTicketNumValidator extends ConstraintValidator {

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * NotAvailableTicketNumValidator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
      $this->em = $em;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
		// custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
		if (!$value instanceof Purchase) {
            return;
        }
  
        if ($this->isUnavailable($value)) {
            $this->context->buildViolation($constraint->message)->atPath('numberOfTickets')->addViolation();
        }
    }

    /**
     * @param Purchase $purchase
     * @return bool
     */
    private function isUnavailable(Purchase $purchase) {
        // As Purchase class constraint we have access to Purchase getters
        $chosenDate = $purchase->getDateOfVisit();
        $addedTickets = $purchase->getNumberOfTickets();

        $totalSoldTickets = $this->em->getRepository('AppBundle:Purchase')->ticketsSoldOnChosenDate($chosenDate);

        if ($totalSoldTickets + $addedTickets > 1000 ) {
            return true;
        }
        return false;
    }
	
}