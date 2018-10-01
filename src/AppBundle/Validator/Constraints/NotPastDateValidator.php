<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use AppBundle\Entity\Purchase;

class NotPastDateValidator extends ConstraintValidator {

  
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

        $today = new \Datetime('now');
        $todayYMD = $today->format('Y-m-d');
		$chosenDateYMD = $chosenDate->format('Y-m-d');
        
        // Past date constraint
		if ($chosenDateYMD < $todayYMD) {
			return true;
		}
		return false;
    }
}