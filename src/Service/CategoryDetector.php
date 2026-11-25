<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use Psr\Log\LoggerInterface;

final class CategoryDetector
{
    /**
     * Категории по умолчанию с ключевыми словами для авто-детекта.
     *
     * @var array<string, string[]>
     */
    private const DEFAULT_CATEGORIES = [
        'Продукты' => [
            'хлеб', 'молоко', 'масло', 'мясо', 'рыба', 'суп', 'крупа',
            'macaroni', 'макароны', 'сахар', 'соль', 'чай', 'кофе',
            'fruit', 'фрукт', 'овощ', 'картош', 'баклаж', 'помид',
            'огур', 'яблок', 'банан', 'citrus', 'цитрус', 'сыр', 'колбас',
            'сосиск', 'птиц', 'йогурт', 'творог', 'кефир', 'сметан',
            'печень', 'конфет', 'шоколад', 'пирож', 'торт', 'морожен',
            'соус', 'приправ', 'специ', 'мука', 'дрожж', 'варень',
        ],
        'Быт' => [
            'средств', 'гель', 'шампун', 'мыло', 'порошок', 'пят',
            'fairy', 'доместос', 'блеск', 'чист', 'моющий', 'пол',
            'туалет', 'бумаж', 'салфет', 'пакет', 'фольг', 'плёнк',
            'пищев', 'квасц', 'губк', 'тряп', 'веник', 'швабр',
        ],
        'Аптека' => [
            'аптек', 'таблет', 'лекарств', 'пластырь', 'вирус', 'грипп',
            'боль в горле', 'кашель', 'температур', 'антигистамин',
            'vitamin', 'витамин', 'мазь', 'капли', 'спрей', 'антисепт',
        ],
        'Транспорт' => [
            'такси', 'uber', 'yandex', 'автобус', 'метро', 'парковка',
            'бензин', 'заправка', 'транспорт', 'пробег', 'автомойк',
            ' шиномонтаж', 'техобслужив', 'страховк', 'штраф',
        ],
        'Развлечения' => [
            'кино', 'театр', 'музей', 'парк', 'аттракцион', 'игра',
            'game', 'подписк', 'streaming', 'netflix', 'spotify', 'youtube',
            'ресторан', 'кафе', 'бар', 'доставк', 'еда', 'пицц',
        ],
        'Одежда' => [
            'одежд', 'обувь', 'кроссовк', 'футболк', 'джинс', 'пальто',
            'куртк', 'штаны', 'юбк', 'платье', 'аксессуар', 'сумк',
        ],
        'Здоровье' => [
            'врач', 'клиник', 'стоматолог', 'медицин', 'анализ', 'рентген',
            'массаж', 'физиотерап', 'оздоровл', 'спорт', 'фитнес',
            'абонемент', 'тренер',
        ],
        'Образование' => [
            'курс', 'обучение', 'учебник', 'школ', 'университет',
            'репетитор', 'семинар', 'конференц', 'подписк', 'журнал',
            'библиотек', 'онлайн', 'webinar', 'вебинар',
        ],
    ];

    public function __construct(
        private readonly ProductCategoryRepository $categoryRepository,
        private readonly LoggerInterface $logger,
    ) {
    }// end __construct()

    /**
     * Определить категорию по названию товара.
     */
    public function detect(string $itemName): ?ProductCategory
    {
        $nameLower = mb_strtolower($itemName, 'UTF-8');

        foreach (self::DEFAULT_CATEGORIES as $categoryName => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($nameLower, mb_strtolower($keyword, 'UTF-8'))) {
                    return $this->categoryRepository->upsert($categoryName, implode(',', $keywords));
                }
            }
        }

        return null;
    }// end detect()

    /**
     * Получить список всех категорий с их ключевыми словами.
     *
     * @return array<string, string[]>
     */
    public function getDefaultCategories(): array
    {
        return self::DEFAULT_CATEGORIES;
    }// end getDefaultCategories()

    /**
     * Добавить пользовательскую категорию.
     */
    public function addCustomCategory(string $name, array $keywords): ProductCategory
    {
        $category = $this->categoryRepository->upsert($name, implode(',', $keywords));
        $this->logger->info(sprintf('Добавлена категория: %s', $name));

        return $category;
    }// end addCustomCategory()
}// end class
