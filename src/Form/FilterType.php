<?php

/**
 * Expenses/Income.
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
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $years = range((int) date('Y'), (int) date('Y') - 10);

        $builder
            ->add(
                'year',
                ChoiceType::class,
                [
                    'choices'                   => array_combine($years, $years),
                    'label'                     => 'label.year',
                    'placeholder'               => 'choose.year',
                    'required'                  => false,
                    'choice_label'              => static fn (int $year): string => "{$year}",
                    'choice_translation_domain' => false,
                ]
            )
            ->add(
                'month',
                ChoiceType::class,
                [
                    'choices'     => Months::getChoices(),
                    'label'       => 'label.month',
                    'placeholder' => 'choose.month',
                    'required'    => false,
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                ['label' => 'action.filtred']
            );
    }// end buildForm()

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }// end configureOptions()
}// end class
