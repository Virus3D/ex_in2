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
use App\Entity\Service;
use App\Enum\Months;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractServiceFormType extends AbstractType
{
    /**
     * Add common fields to the form builder.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param Place                $place   The place entity
     */
    protected function addCommonFields(FormBuilderInterface $builder, Place $place): void
    {
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
                'amount',
                MoneyType::class,
                [
                    'currency' => '',
                    'divisor'  => 100,
                    'input'    => 'integer',
                    'scale'    => 2,
                ]
            )
            ->add(
                'service',
                EntityType::class,
                [
                    'class'        => Service::class,
                    'choices'      => $place->getServices(),
                    'choice_label' => static fn (?Service $service): string => $service?->getName() ?? '',
                ]
            );
    }//end addCommonFields()
}//end class
