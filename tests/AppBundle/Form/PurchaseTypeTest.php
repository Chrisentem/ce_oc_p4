<?php

namespace Tests\AppBundle\Form;

use AppBundle\Form\PurchaseType;
use AppBundle\Entity\Purchase;
use Symfony\Component\Form\Test\TypeTestCase;

class PurchaseTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            // Use if js-datepicker disabled and date format must be without leading zeros.
            // 'dateOfVisit' => ['day' => '3','month' => '3','year' => '2019'],
            'dateOfVisit' => '2019-03-03',
            'ticketType' =>  Purchase::FULL_DAY_TICKET_TYPE,
            'numberOfTickets' => '5',
        );
        // Here we arbitrary create a date to override the Purchase constructor
        // that would lead to a synchronization problem between $objectToCompare
        // and $object instantiation
        $createDate = new \DateTime();

        $objectToCompare = new Purchase();
        $objectToCompare->setDate($createDate);
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(PurchaseType::class, $objectToCompare);

        $object = new Purchase();
        // ...populate $object properties with the data stored in $formData
        // Override the Purchase __construct "date"
        $object->setDate($createDate);
        $object->setDateOfVisit(new \DateTime('2019-03-03'));
        $object->setTicketType(Purchase::FULL_DAY_TICKET_TYPE);
        $object->setNumberOfTickets(5);

        // submit the data to the form directly
        $form->submit($formData);
        //dump($objectToCompare);
        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
