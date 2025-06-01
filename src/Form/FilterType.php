<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $years = range((int) date('Y'), (int) date('Y') - 10);

        $months = [
            'January'   => 1,
            'February'  => 2,
            'March'     => 3,
            'April'     => 4,
            'May'       => 5,
            'June'      => 6,
            'July'      => 7,
            'August'    => 8,
            'September' => 9,
            'October'   => 10,
            'November'  => 11,
            'December'  => 12,
        ];

        $builder
            ->add(
                'year',
                ChoiceType::class,
                [
                    'choices'      => array_combine($years, $years),
                    'placeholder'  => 'Choose a year',
                    'required'     => false,
                    'choice_label' => static fn (int $year): string => "{$year}",
                ]
            )
            ->add(
                'month',
                ChoiceType::class,
                [
                    'choices'     => $months,
                    'placeholder' => 'Select a month',
                    'required'    => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Filter',
                ]
            );
    }//end buildForm()

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }//end configureOptions()
}//end class
