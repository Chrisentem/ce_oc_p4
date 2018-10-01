<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
                'data' => new \DateTime(),
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
            'choices' => array_combine(range(1,6),range(1,6)),
            )
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Purchase'
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
