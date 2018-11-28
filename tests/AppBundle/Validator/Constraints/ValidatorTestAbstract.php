<?php
namespace Tests\AppBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

/**
 * Class ValidatorTestAbstract.
 */
abstract class ValidatorTestAbstract extends \PHPUnit\Framework\TestCase
{
    /**
     * Returns an instance of validator to test.
     *
     * @return ConstraintValidator
     */
    abstract protected function getValidatorInstance();

    /**
     * Initializes validator.
     *
     * @param string|null $expectedMessage the expected violation message (null if waiting for value validation)
     *
     * @return ConstraintValidator
     */
    protected function initValidator($expectedMessage = null)
    {
        $builder = $this->getMockBuilder(ConstraintViolationBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['addViolation'])
            ->getMock();

        $context = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildViolation'])
            ->getMock();

        if ($expectedMessage) {
            $builder->expects($this->once())->method('addViolation');

            $context->expects($this->once())
                ->method('buildViolation')
                ->with($this->equalTo($expectedMessage))
                ->will($this->returnValue($builder));
        } else {
            $context->expects($this->never())->method('buildViolation');
        }

        $validator = $this->getValidatorInstance();
        /* @var ExecutionContext $context */
        $validator->initialize($context);

        return $validator;
    }
}