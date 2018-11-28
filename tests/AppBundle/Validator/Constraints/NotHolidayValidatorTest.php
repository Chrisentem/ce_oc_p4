<?php

namespace Tests\AppBundle\Validator\Constraints;


use AppBundle\Entity\Purchase;
use AppBundle\Validator\Constraints\NotHoliday;
use AppBundle\Validator\Constraints\NotHolidayValidator;

/**
 * Class NotHolidayValidatorTest.
 */
class NotHolidayValidatorTest extends ValidatorTestAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function getValidatorInstance()
    {
        return new NotHolidayValidator();
    }

    /**
     * Testing of valid days
     * @dataProvider dateProviderOk
     * @param $date
     */
    public function testValidationOk($date)
    {
        $notHolidayConstraint = new NotHoliday();
        $notHolidayValidator = $this->initValidator();

        $purchase = new Purchase();
        $purchase->setDateOfVisit($date);
        $notHolidayValidator->validate($purchase, $notHolidayConstraint);
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
        $notHolidayConstraint = new NotHoliday();

        $notHolidayValidator = $this->initValidator($notHolidayConstraint->message);

        $purchase = new Purchase();
        $purchase->setDateOfVisit($date);

        $notHolidayValidator->validate($purchase, $notHolidayConstraint);

    }

    /**
     * @return array
     */
    public function dateProviderKo()
    {
        return [
            [new \Datetime('2019-01-01')],
            [new \Datetime('2019-07-14')],
            [new \Datetime('2019-11-11')],
        ];
    }
}