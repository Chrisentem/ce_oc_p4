<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Dateavailable extends Constraint {

    public $message = "Le musée est fermé à cette date, veuillez choisir une autre date !";

}