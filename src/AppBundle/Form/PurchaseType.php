<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Purchase;

class PurchaseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('dateOfVisit', DateType::class, array(
                'label' => 'Date of visit',
                'widget' => 'choice',
                'years' => range(date('Y'), date('Y')+1),
            )
        )
        ->add('ticketType', ChoiceType::class, array(
            'label' => 'Ticket Type',
            'choices'  => array(
                'full-day' => 0,
                'half-day' => 1,
            ))
        )
        ->add('numberOfTickets', ChoiceType::class, array(
            'label' => 'Qty',
            'required' => true,
            'choices' => array_combine(range(1,Purchase::MAX_PURCHASE_TICKETS),range(1,Purchase::MAX_PURCHASE_TICKETS)),
            )
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Purchase::class,            
            'validation_groups' => array('step1'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_purchase';
    }


}
