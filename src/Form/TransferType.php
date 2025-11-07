<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Form;

use App\Entity\Card;
use App\Entity\Transfer;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'date',
                DateType::class,
                [
                    'data' => new DateTime(),
                ]
            )
            ->add(
                'balance',
                MoneyType::class,
                [
                    'currency' => '',
                    'divisor'  => 100,
                    'input'    => 'integer',
                    'scale'    => 2,
                ]
            )
            ->add(
                'cardOut',
                EntityType::class,
                [
                    'class'        => Card::class,
                    'choice_label' => static fn (?Card $card): string => $card?->getName() ?? '',
                    'group_by'     => static fn (?Card $card): string => $card?->getCategory()->getName() ?? '',
                ]
            )
            ->add(
                'cardIn',
                EntityType::class,
                [
                    'class'        => Card::class,
                    'choice_label' => static fn (?Card $card): string => $card?->getName() ?? '',
                    'group_by'     => static fn (?Card $card): string => $card?->getCategory()->getName() ?? '',
                ]
            )
        ;
    }//end buildForm()

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Transfer::class,
            ]
        );
    }//end configureOptions()
}//end class
