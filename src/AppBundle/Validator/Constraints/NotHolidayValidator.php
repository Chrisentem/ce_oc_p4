<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use AppBundle\Entity\Purchase;

class NotHolidayValidator extends ConstraintValidator {

  
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
	
	public function isUnavailable(Purchase $purchase) {
		// As Purchase class constraint we have access to Purchase getters
		$chosenDate = $purchase->getDateOfVisit();
		$year = $chosenDate->format('Y');

		$closedDays = $this->getHolidaysList($year);
		$chosenDateYMJ = $chosenDate->format('Y-m-j');
	
		// Holidays constraint
		if (in_array($chosenDateYMJ, $closedDays)) {
			return true;
		}
		
		return false;
	}
	

	private function getHolidaysList($year = null) {

		if ($year === null)
		{
			$year = intval(date('Y'));
		}
		
		$easterDate  = easter_date($year);
		$easterDay   = date('j', $easterDate);
		$easterMonth = date('n', $easterDate);
		$easterYear   = date('Y', $easterDate);
		
		$holidays = array(
			// Fixed Dates
			date('Y-m-j',mktime(0, 0, 0, 1,  1,  $year)),  // 1er janvier
			date('Y-m-j',mktime(0, 0, 0, 5,  1,  $year)),  // Fête du travail
			date('Y-m-j',mktime(0, 0, 0, 5,  8,  $year)),  // Victoire des alliés
			date('Y-m-j',mktime(0, 0, 0, 7,  14, $year)),  // Fête nationale
			date('Y-m-j',mktime(0, 0, 0, 8,  15, $year)),  // Assomption
			date('Y-m-j',mktime(0, 0, 0, 11, 1,  $year)),  // Toussaint
			date('Y-m-j',mktime(0, 0, 0, 11, 11, $year)),  // Armistice
			date('Y-m-j',mktime(0, 0, 0, 12, 25, $year)),  // Noel

			// Moving Dates
			date('Y-m-j',mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear)),
			date('Y-m-j',mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)),
			date('Y-m-j', mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear)),
		);
		
		sort($holidays);
		
		return $holidays;
	}
}