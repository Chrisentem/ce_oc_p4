<?php

namespace Tests\AppBundle\Validator\Constraints;


use AppBundle\Entity\Purchase;
use AppBundle\Validator\Constraints\NotSunday;
use AppBundle\Validator\Constraints\NotSundayValidator;

/**
 * Class NotSundayValidatorTest.
 */
class NotSundayValidatorTest extends ValidatorTestAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function getValidatorInstance()
    {
        return new NotSundayValidator();
    }

    /**
     * Testing of valid days
     * @dataProvider dateProviderOk
     * @param $date
     */
    public function testValidationOk($date)
    {
        $notSundayConstraint = new NotSunday();
        $notSundayValidator = $this->initValidator();

        $purchase = new Purchase();
        $purchase->setDateOfVisit($date);
        $notSundayValidator->validate($purchase, $notSundayConstraint);
    }

    /**
     * @return array
     */
    public function dateProviderOk()
    {
        return [
            [new \Datetime('2019-01-09')],
            [new \Datetime('2019-02-15')],
            [new \Datetime('2019-03-18')],
        ];
    }

    /**
     * Testing invalid days
     * @dataProvider dateProviderKo
     * @param $date
     */
    public function testValidationKo($date)
    {
        $notSundayConstraint = new NotSunday();

        $notSundayValidator = $this->initValidator($notSundayConstraint->message);

        $purchase = new Purchase();
        $purchase->setDateOfVisit($date);

        $notSundayValidator->validate($purchase, $notSundayConstraint);

    }

    /**
     * @return array
     */
    public function dateProviderKo()
    {
        return [
            [new \Datetime('2019-01-06')],
            [new \Datetime('2019-02-17')],
            [new \Datetime('2019-04-21')],
        ];
    }
}