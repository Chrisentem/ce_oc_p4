<?php

namespace AppBundle\Form;

use AppBundle\Entity\Purchase;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class MultiTicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tickets', CollectionType::class, array(
            'translation_domain' => 'forms',
            'label' => 'label.tickets',
            'error_bubbling' => false,
            'entry_type' => EntryTicketType::class,
            'entry_options' => array('label' => false),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Purchase::class,            
            'validation_groups' => array('step1','step2'),
        ));
    }

}
