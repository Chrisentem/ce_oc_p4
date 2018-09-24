<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TickettypeavailableValidator extends ConstraintValidator {

  
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
	
	public function isUnavailable($type) {
		
        $today = new \Datetime('now');
        $nowtime = $today->format('H');
		
		// No full-day ticket after 2pm
		if ($type == 'fullday' && $nowtime > 14) {
			return true;
		}
		
		return false;
    }
}