<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotInTimeForCurrentDay extends Constraint {

    public $message = "Trop tard pour aujourd'hui, le musée a fermé ses portes !";

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}