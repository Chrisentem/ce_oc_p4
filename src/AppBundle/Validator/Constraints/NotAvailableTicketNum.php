<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotAvailableTicketNum extends Constraint {

    public $message = "Il n'y a plus assez de billets disponibles pour cette date";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}