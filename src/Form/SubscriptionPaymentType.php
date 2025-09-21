<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Form;

use App\Entity\Card;
use App\Entity\Subscription;
use App\Entity\SubscriptionPayment;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SubscriptionPaymentType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                'subscrip',
                EntityType::class,
                [
                    'class'        => Subscription::class,
                    'choice_label' => static fn (?Subscription $subscription): string => $subscription?->getName() ?? '',
                ]
            )
            ->add(
                'card',
                EntityType::class,
                [
                    'class'        => Card::class,
                    'choice_label' => static fn (?Card $card): string => $card?->getName() ?? '',
                    'group_by'     => static fn (?Card $card): string => $card?->getCategory()->getName() ?? '',
                ]
            )
            ->add(
                'date',
                DateType::class,
                [
                    'data' => new DateTime(),
                ]
            );

        // Очистка данных от пробелов в поле amount.
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            static function (FormEvent $event): void {
                $data = $event->getData();
                if (isset($data['amount'])) {
                    $data['amount'] = preg_replace('/\s+/', '', $data['amount']);
                }

                $event->setData($data);
            }
        );
    }//end buildForm()

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => SubscriptionPayment::class,
            ]
        );
    }//end configureOptions()
}//end class
