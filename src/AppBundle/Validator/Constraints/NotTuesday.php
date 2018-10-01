<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotTuesday extends Constraint {

    public $message = "Le musée est fermé les mardis, veuillez choisir une autre date !";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}