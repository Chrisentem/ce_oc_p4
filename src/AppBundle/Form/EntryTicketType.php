<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class EntryTicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array(
                    'translation_domain' => 'forms',
                    'label' => 'label.firstname',)
            )
            ->add('lastname', TextType::class, array(
                    'translation_domain' => 'forms',
                    'label' => 'label.lastname',)
            )
            ->add('country', CountryType::class, array(
                    'translation_domain' => 'forms',
                    'label' => 'label.country',)
            )
            ->add('birthdate', BirthdayType::class, array(
                    'translation_domain' => 'forms',
                    'label' => 'label.date.birth',)
            )
            ->add('discounted', CheckboxType::class, array(
                'translation_domain' => 'forms',
                'label'    => 'label.price.reduced',
                'required' => false,
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\EntryTicket'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_entryticket';
    }


}
