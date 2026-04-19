<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Form;

use App\Entity\Place;
use App\Entity\ServiceMeterReading;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Service;
use App\Enum\Months;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ServiceMeterReadingType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $place = $options['place'];

        $builder
            ->add(
                'month',
                ChoiceType::class,
                [
                    'choices'     => Months::getChoices(),
                    'placeholder' => 'Select a month',
                ]
            )
            ->add(
                'reading',
                IntegerType::class,
                [
                    'label' => 'Reading',
                    'attr'  => ['placeholder' => '0'],
                ]
            )
            ->add(
                'service',
                EntityType::class,
                [
                    'class'        => Service::class,
                    'choices'      => $place->getServices()->filter(
                        static fn (Service $service): bool => $service->getHasMeter()
                    ),
                    'choice_label' => static fn (?Service $service): string => $service?->getName() ?? '',
                ]
            );
    }// end buildForm()

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('place');
        $resolver->setAllowedTypes('place', Place::class);
        $resolver->setDefaults(
            [
                'data_class' => ServiceMeterReading::class,
            ]
        );
    }// end configureOptions()
}// end class
