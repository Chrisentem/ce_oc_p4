<?php

namespace Tests\AppBundle\Form;

use AppBundle\Form\ContactEmailType;
use AppBundle\Entity\ContactEmail;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactEmailTypeTest extends TypeTestCase
{
    private $validator;

    protected function getExtensions()
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        // use getMock() on PHPUnit 5.3 or below
        // $this->validator = $this->getMock(ValidatorInterface::class);
        $this->validator
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));
        $this->validator
            ->method('getMetadataFor')
            ->will($this->returnValue(new ClassMetadata(Form::class)));

        return array(
            new ValidatorExtension($this->validator),
        );
    }

    public function testSubmitValidData()
    {
        $formData = array(
            'subject' => 'test',
            'content' => 'test2',
            'email' => 'test@test.test',
            'name' => 'Tester',
            'phone' => '014565465'
        );
        // Here we arbitrary create a date to override the ContactEmail constructor
        // that would lead to a synchronization problem between $objectToCompare
        // and $object instantiation
        $createDate = new \DateTime();

        $objectToCompare = new ContactEmail();
        $objectToCompare->setDate($createDate);
        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ContactEmailType::class, $objectToCompare);

        $object = new ContactEmail();
        // ...populate $object properties with the data stored in $formData
        $object->setSubject('test');
        $object->setName('Tester');
        $object->setDate($createDate);
        $object->setContent('test2');
        $object->setPhone('014565465');

        // submit the data to the form directly
        $form->submit($formData);

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
