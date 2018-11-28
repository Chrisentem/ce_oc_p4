<?php

namespace AppBundle\Validator\Constraints;

use AppBundle\Service\Time;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use AppBundle\Entity\Purchase;

class NotInTimeForCurrentDayValidator extends ConstraintValidator
{

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
    private function isUnavailable(Purchase $purchase)
    {
        // We use the Time class to generate a current time usable with ClockMock for testing
        // don't use new \Datetime('now') or time sensitive test will be complicated
        $today = Time::currentDateTime()->format('U');
        $nowTime = date('H:i', $today);
        $nowDay = date('D', $today);
        // As Purchase class constraint we have access to Purchase getters
        $chosenDate = $purchase->getDateOfVisit();

        $todayDNY = date('d-n-Y', $today);
        $chosenDateDNY = $chosenDate->format('d-n-Y');

        // No booking for current day 1 hour before closing times
        if ($todayDNY == $chosenDateDNY) {
            if (in_array($nowDay, ['Mon', 'Thu', 'Sat'])) {
                if ($nowTime > '17:00') {
                    return true;
                }
            } elseif (in_array($nowDay, ['Wed', 'Fri'])) {
                if ($nowTime > '20:45') {
                    return true;
                }
            }
        }
        return false;
    }
}
