<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateavailableValidator extends ConstraintValidator {

  
    public function validate($value, Constraint $constraint)
    {
		// custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
		if (null === $value || '' === $value) {
            return;
        }
			  
		if ($this->isUnavailable($value)) {
			$this->context->addViolation($constraint->message);
		}

    }
	
	public function isUnavailable($date) {
		
		// closed days constraint
		$closedDays = ['01-05', '01-11', '25-12'];
		$chosenDateDM = $date->format('d-m');
		
		// Passed day constraint
        $today = new \Datetime('now');
        $todayYMD = $today->format('Y-m-d');
		$chosenDateYMD = $date->format('Y-m-d');
		
		// closed on Tuesday constraint
		$chosenDateD = $date->format('D');
		
		
		if (in_array($chosenDateDM, $closedDays) || $chosenDateYMD < $todayYMD || $chosenDateD == "Tue") {
			return true;
		}
		
		return false;
    }
}