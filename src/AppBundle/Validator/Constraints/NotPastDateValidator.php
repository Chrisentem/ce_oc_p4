<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Service\Time;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use AppBundle\Entity\Purchase;

class NotPastDateValidator extends ConstraintValidator {


    /**
     * @param $value
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
            $this->context->buildViolation($constraint->message)->atPath('dateOfVisit')->addViolation();
        }
    }

    /**
     * @param Purchase $purchase
     * @return bool
     */
    private function isUnavailable(Purchase $purchase) {
        // As Purchase class constraint we have access to Purchase getters
        $chosenDate = $purchase->getDateOfVisit();

        // We use the Time class to generate a current time usable with ClockMock for testing
        // don't use new \Datetime('now') or time sensitive test will be complicated
        $today = Time::currentDateTime()->format('U');
        $todayYMD = date('Y-m-d', $today);
        $chosenDateYMD = $chosenDate->format('Y-m-d');

        // Past date constraint
        if ($chosenDateYMD < $todayYMD) {
            return true;
        }
        return false;
    }

}