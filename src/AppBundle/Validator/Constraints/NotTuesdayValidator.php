<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use AppBundle\Entity\Purchase;

class NotTuesdayValidator extends ConstraintValidator {

  
    public function validate($value, Constraint $constraint)
    {
		// custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
		if (!$value instanceof Purchase) {
            return;
        }
			  
		if ($this->isUnavailable($value)) {
			$this->context->buildViolation($constraint->message)->atPath('dateOfVisit')->addViolation();
		}
    }
	
	private function isUnavailable(Purchase $purchase) {
		// As Purchase class constraint we have access to Purchase getters
        $chosenDate = $purchase->getDateOfVisit();

		$chosenDateD = $chosenDate->format('D');
		
		// Closed on Tuedsday constraint
		if ($chosenDateD == "Tue") {
			return true;
		}
		return false;
    }
}