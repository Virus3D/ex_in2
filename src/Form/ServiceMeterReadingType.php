<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Form;

use App\Entity\ServiceMeterReading;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Service;

final class ServiceMeterReadingType extends AbstractServiceFormType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $place = $options['place'];

        $this->addCommonFields($builder, $place);

        $builder
            ->remove('amount')
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
        parent::configureOptions($resolver);

        $resolver->setDefaults(['data_class' => ServiceMeterReading::class]);
    }// end configureOptions()
}// end class
