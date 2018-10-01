<?php

namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class NotAvailableTicketNumValidator extends ConstraintValidator {

    /**
     * @var EntityManagerInterface
     */
    private $em;
  
    // Les arguments déclarés dans la définition du service arrivent au constructeur
    // On doit les enregistrer dans l'objet pour pouvoir s'en resservir dans la méthode validate()
    public function __construct(EntityManagerInterface $em)
    {
      $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
		// custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
		if (!$value instanceof Purchase) {
            return;
        }
  
        if ($totalsoldTickets + $addedTickets > 1000 ) {
            $this->context->buildViolation($constraint->message)->atPath('numberOfTickets')->addViolation();
        }
    }

    private function isUnavailable(Purchase $purchase) {
        // As Purchase class constraint we have access to Purchase getters
        $chosenDate = $purchase->getDateOfVisit();
        $addedTickets = $purchase->getNumberOfTickets();

        $totalsoldTickets = $this->em->getRepository('AppBundle:Purchase')->ticketsSoldOnChosenDate($chosenDate);

        if ($totalsoldTickets + $addedTickets > 1000 ) {
            return true;
        }
        return false;
    }
	
}