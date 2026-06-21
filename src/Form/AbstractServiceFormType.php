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
use App\Helper\FilterDataHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractServiceFormType extends AbstractType
{
    public function __construct(
        private FilterDataHelper $filterDataHelper,
    ) {
    }// end __construct()

    /**
     * Добавление общих полей в построитель формы.
     *
     * Метод добавляет три основных поля:
     * - month - выбор месяца из перечисления Months
     * - amount - поле для ввода суммы (в копейках)
     * - service - выбор услуги из списка доступных для данного места
     *
     * @param FormBuilderInterface $builder      Построитель формы
     * @param Place                $place        Место (площадка/филиал), для которого создается запись
     * @param int|null             $defaultMonth Значение месяца по умолчанию (если не указан - предыдущий месяц)
     * @param int|null             $defaultYear  Значение года по умолчанию (если не указан - вычисляется автоматически)
     */
    protected function addCommonFields(
        FormBuilderInterface $builder,
        Place $place,
        ?int $defaultMonth = null,
        ?int $defaultYear = null,
    ): void {
        // Определяем значения по умолчанию для месяца и года.
        $monthData = $defaultMonth ?? $this->filterDataHelper->monthPrev;
        $yearData  = $defaultYear ?? $this->filterDataHelper->yearPrev;

        $builder
            ->add(
                'year',
                ChoiceType::class,
                [
                    'choices'                   => $this->getYearChoices(),
                    'data'                      => $yearData,
                    'placeholder'               => 'choose.year',
                    'choice_translation_domain' => false,
                ]
            )
            ->add(
                'month',
                ChoiceType::class,
                [
                    'choices'     => Months::getChoices(),
                    'placeholder' => 'choose.month',
                    'data'        => $monthData,
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
    }// end addCommonFields()

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'default_month' => null,
                'default_year'  => null,
            ]
        );
        $resolver->setRequired('place');
        $resolver->setAllowedTypes('place', Place::class);
    }// end configureOptions()

    /**
     * Получение списка годов для выбора.
     *
     * Создает диапазон годов: от (текущий год - 5) до (текущий год + 1).
     * Это позволяет вводить данные за прошлые годы и на год вперед.
     *
     * @return array<string, int> Ассоциативный массив [год => год]
     */
    protected function getYearChoices(): array
    {
        $currentYear = (int) (new \DateTime())->format('Y');
        $years = [];

        // Создаем диапазон от текущего года - 5 до текущего года.
        for ($year = $currentYear - 5; $year <= $currentYear; $year++) {
            $years[(string) $year] = $year;
        }

        return $years;
    }// end getYearChoices()
}// end class
