<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CardCategory;
use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Сервис для кеширования редко меняющихся данных
 */
class StaticDataCache
{
    /**
     * Массив категорий карт с данными или null, если еще не загружено.
     *
     * @var array<int, array<string, mixed>>|null
     */
    private static ?array $categories = null;

    /**
     * Массив мест с данными или null, если еще не загружено.
     *
     * @var array<int, array<string, mixed>>|null
     */
    private static ?array $places = null;

    public function __construct(
        private EntityManagerInterface $em,
        private CacheInterface $cache
    ) {
    }// end __construct()

    /**
     * Получить список категорий карт.
     *
     * @return array<int, array<string, mixed>> Массив категорий карт с данными
     */
    public function getCategories(): array
    {
        if (self::$categories === null) {
            self::$categories = $this->cache->get(
                'categories_forever',
                function (ItemInterface $item) {
                    $item->expiresAfter(31536000);
                // 1 год
                    return $this->em->getRepository(CardCategory::class)
                        ->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC')
                        ->getQuery()
                        ->getArrayResult();
                }
            );
        }

        return self::$categories;
    }// end getCategories()

    /**
     * Получить список мест
     *
     * @return array<int, array<string, mixed>> Массив мест с данными
     */
    public function getPlaces(): array
    {
        if (self::$places === null) {
            self::$places = $this->cache->get(
                'places_forever',
                function (ItemInterface $item) {
                    $item->expiresAfter(31536000);
                // 1 год
                    return $this->em->getRepository(Place::class)
                        ->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC')
                        ->getQuery()
                        ->getArrayResult();
                }
            );
        }

        return self::$places;
    }// end getPlaces()

    /**
     * Clears the cached categories and places data.
     *
     * This method deletes the relevant cache entries and resets
     * the static cached arrays so fresh data will be fetched next time.
     */
    public function clearCache(): void
    {
        $this->cache->delete('categories_forever');
        $this->cache->delete('places_forever');
        self::$categories = null;
        self::$places = null;
    }// end clearCache()
}// end class
