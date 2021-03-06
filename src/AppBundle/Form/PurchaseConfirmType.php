<?php

namespace AppBundle\Form;

use AppBundle\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;



class PurchaseConfirmType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', RepeatedType::class, array(
            'translation_domain' => 'forms',
            'label' => 'label.email',
            'type' => EmailType::class,
            'invalid_message' => 'invalid.email',
            'options' => array('attr' => array('class' => 'email-field')),
            'required' => true,
            'first_options'  => array('label' => 'label.email'),
            'second_options' => array('label' => 'label.email.repeat'),
            ))
            ->add('agree', CheckboxType::class, array(
                'translation_domain' => 'forms',
                'label'    => 'label.agreement',
                'required' => true,
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Purchase::class,
            'validation_groups' => array('step1','step2','step3'),
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
