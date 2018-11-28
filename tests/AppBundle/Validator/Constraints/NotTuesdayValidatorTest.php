<?php

namespace Tests\AppBundle\Validator\Constraints;


use AppBundle\Entity\Purchase;
use AppBundle\Validator\Constraints\NotTuesday;
use AppBundle\Validator\Constraints\NotTuesdayValidator;

/**
 * Class NotTuesdayValidatorTest.
 */
class NotTuesdayValidatorTest extends ValidatorTestAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function getValidatorInstance()
    {
        return new NotTuesdayValidator();
    }

    /**
     * Testing of valid days
     * @dataProvider dateProviderOk
     * @param $date
     */
    public function testValidationOk($date)
    {
        $notTuesdayConstraint = new NotTuesday();
        $notTuesdayValidator = $this->initValidator();

        $purchase = new Purchase();
        $purchase->setDateOfVisit($date);
        $notTuesdayValidator->validate($purchase, $notTuesdayConstraint);
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
        $notTuesdayConstraint = new NotTuesday();

        $notTuesdayValidator = $this->initValidator($notTuesdayConstraint->message);

        $purchase = new Purchase();
        $purchase->setDateOfVisit($date);

        $notTuesdayValidator->validate($purchase, $notTuesdayConstraint);

    }

    /**
     * @return array
     */
    public function dateProviderKo()
    {
        return [
            [new \Datetime('2019-01-08')],
            [new \Datetime('2019-02-19')],
            [new \Datetime('2019-04-23')],
        ];
    }
}