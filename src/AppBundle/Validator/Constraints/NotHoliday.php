<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotHoliday extends Constraint {

    public $message = "Le musée est fermé les jours fériés, veuillez choisir une autre date !";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}