<?php

namespace Autobus\Bundle\BusBundle\Form;

use Autobus\Bundle\BusBundle\Entity\JobGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $runnerChain = $options['runner_chain'];

        $runnerClasses = [];
        foreach ($runnerChain->getRunners() as $sid => $runner) {
            $runnerClasses[$sid] = $runner['label'];
        }

        $builder
          ->add('name')
          ->add('runner', ChoiceType::class, ['choices' => array_flip($runnerClasses)])
          ->add(
              'group',
              EntityType::class,
              ['placeholder' => 'Choose ...', 'required' => false, 'class' => JobGroup::class]
          )
          ->add('trace')
          ->add('config');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('runner_chain');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'AutobusBusBundle_job';
    }
}
