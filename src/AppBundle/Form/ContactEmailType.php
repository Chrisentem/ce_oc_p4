<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactEmailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subject', TextType::class, array(
                'translation_domain' => 'forms',
                'label' => 'label.subject',)
        )
            ->add('content', TextareaType::class, array(
                    'translation_domain' => 'forms',
                    'label' => 'label.content',)
            )
            ->add('email', RepeatedType::class, array(
                'translation_domain' => 'forms',
                'label' => 'label.email',
                'type' => EmailType::class,
                'invalid_message' => 'invalid.email',
                'options' => array('attr' => array('class' => 'email-field')),
                'required' => true,
                'first_options'  => array('label' => 'label.email'),
                'second_options' => array('label' => 'label.email.repeat'),
            ))
            ->add('name', TextType::class, array(
                'translation_domain' => 'forms',
                'label' => 'label.name',
                'required' => false))
            ->add('phone', TelType::class, array(
                'translation_domain' => 'forms',
                'label' => 'label.phone',
                'required' => false));
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ContactEmail'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contactemail';
    }


}
