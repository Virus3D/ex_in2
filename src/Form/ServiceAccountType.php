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
use App\Entity\ServiceAccount;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ServiceAccountType extends AbstractServiceFormType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $place = $options['place'];

        $this->addCommonFields($builder, $place);

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
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('place');
        $resolver->setAllowedTypes('place', Place::class);
        $resolver->setDefaults(
            [
                'data_class' => ServiceAccount::class,
            ]
        );
    }//end configureOptions()
}//end class
