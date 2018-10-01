<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotAvailableTicketType extends Constraint {

    public $message = "Ce type de billet n'est plus disponible après 14h, veuillez choisir demi-journée.";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}