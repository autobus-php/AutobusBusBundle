<?php

namespace Autobus\Bundle\BusBundle\Form;

use Autobus\Bundle\BusBundle\Entity\JobGroup;
use Autobus\Bundle\BusBundle\Runner\RunnerCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JobType extends AbstractType implements JobTypeInterface
{
    /**
     * @var RunnerCollection
     */
    protected $runnerCollection;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $job              = $options['data'];
        $runners          = $this->runnerCollection->getRunners($job->getType());
        $availableRunners = [];
        foreach ($runners as $runner) {
            $availableRunners[get_class($runner)] = get_class($runner);
        }

        $builder
            ->add('name')
            ->add('runner',
                ChoiceType::class,
                [
                    'choices' => $availableRunners,
                ]
            )
            ->add(
                'group',
                EntityType::class,
                [
                    'placeholder' => 'Choose ...',
                    'required'    => false,
                    'class'       => JobGroup::class,
                ]
            )
            ->add(
                'trace',
                ChoiceType::class,
                [
                    'choices' => ['Yes' => 1, 'No' => 0],
                ]
            )
            ->add(
                'emailAlert',
                ChoiceType::class,
                [
                    'choices' => ['Yes' => 1, 'No' => 0],
                ]
            )
            ->add(
                'recipients',
                TextType::class
            )
            ->add(
                'config',
                HiddenType::class
            );
    }

    /**
     * @param RunnerCollection $runnerCollection
     *
     * @required
     */
    public function setRunnerCollection(RunnerCollection $runnerCollection)
    {
        $this->runnerCollection = $runnerCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'AutobusBusBundle_job';
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool
    {
        return false;
    }
}
