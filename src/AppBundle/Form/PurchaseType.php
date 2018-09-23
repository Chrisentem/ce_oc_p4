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
        ->add('visitType', ChoiceType::class, array(
            'label' => 'Ticket Type',
            'choices'  => array(
                'full-day' => true,
                'half-day' => false,
            ))
        )
        ->add('numberOfTickets', ChoiceType::class, array(
            'label' => 'Qty',
            'required' => true,
            'choices' => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
            ),
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
