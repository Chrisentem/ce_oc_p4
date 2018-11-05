<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use AppBundle\Entity\Purchase;

class NotAvailableTicketTypeValidator extends ConstraintValidator {

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
            $this->context->buildViolation($constraint->message)->atPath('ticketType')->addViolation();
		}
    }

    /**
     * @param Purchase $purchase
     * @return bool
     */
    private function isUnavailable(Purchase $purchase) {
		
        $today = new \Datetime('now');
        $nowTime = $today->format('H');
        // As Purchase class constraint we have access to Purchase getters
        $chosenDate = $purchase->getDateOfVisit();
        $type = $purchase->getTicketType();
        
        $todayDNY = $today->format('d-n-Y');
        $chosenDateDNY = $chosenDate->format('d-n-Y');
        
    	// No full-day ticket after 2pm
		if ($todayDNY == $chosenDateDNY) {
            if ($type == Purchase::FULL_DAY_TICKET_TYPE && $nowTime > 14) {
            return true;
            }
		}
		return false;
    }
}