<?php

/**
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Form;

use App\Entity\Service;
use App\Entity\ServiceAccount;
use App\Helper\FilterDataHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ServiceAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
                'month',
                ChoiceType::class,
                [
                    'choices'     => $months,
                    'placeholder' => 'Select a month',
                    'data'        => FilterDataHelper::$month,
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
                    'choice_label' => static fn (?Service $service): string => $service?->getName() ?? '',
                ]
            )
        ;

        // Очистка данных от пробелов в поле amount
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            static function (FormEvent $event): void
            {
                $data = $event->getData();
                if (isset($data['amount']))
                {
                    $data['amount'] = preg_replace('/\s+/', '', $data['amount']);
                }

                $event->setData($data);
            }
        );
    }//end buildForm()

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => ServiceAccount::class,
            ]
        );
    }//end configureOptions()
}//end class
