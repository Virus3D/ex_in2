<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Form;

use App\Enum\Months;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FilterType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $years = range((int) date('Y'), (int) date('Y') - 10);

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
                    'choices'     => Months::getChoices(),
                    'placeholder' => 'Select a month',
                    'required'    => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                ['label' => 'Filter']
            );
    }//end buildForm()

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }//end configureOptions()
}//end class
