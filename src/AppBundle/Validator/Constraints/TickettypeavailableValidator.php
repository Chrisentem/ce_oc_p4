<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\HttpFoundation\RequestStack;

class TickettypeavailableValidator extends ConstraintValidator {

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
      $this->requestStack = $requestStack;
    }

  
    public function validate($value, Constraint $constraint)
    {
		// custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
		if (null === $value || '' === $value) {
            return;
        }
			  
		if ($this->isUnavailable($value)) {
			$this->context->addViolation($constraint->message);
		}

    }
	
	public function isUnavailable($type) {
		
        $today = new \Datetime('now');
        $nowtime = $today->format('H');

        $request = $this->requestStack->getCurrentRequest();
        // get dateOfVisit as array
        $chosenDate = $request->request->get('appbundle_purchase')['dateOfVisit'];
        
        $todayDMY = $today->format('d-n-Y');
        $chosenDateDMY = implode('-', $chosenDate);
        
        // var_dump($chosenDateDMY); string(9) "24-9-2018"
        // var_dump($todayDMY);
        
		// No full-day ticket after 2pm
		if ($todayDMY == $chosenDateDMY) {
            if ($type == 'fullday' && $nowtime > 14) {
            return true;
            }
		}
		
		return false;
    }
}