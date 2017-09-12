<?php

namespace Autobus\Bundle\BusBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebJobType extends JobType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
          ->add('path')
          ->add('secure')
          ->add(
              'methods',
              ChoiceType::class,
              ['multiple' => true, 'choices' => ['GET' => 'GET', 'POST' => 'POST'], 'expanded' => true]
          );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            array(
            'data_class' => 'Autobus\Bundle\BusBundle\Entity\WebJob',
            )
        );
    }
}
