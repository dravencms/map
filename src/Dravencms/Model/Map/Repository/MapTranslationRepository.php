<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Map\Repository;

use Dravencms\Model\Map\Entities\Map;
use Dravencms\Model\Map\Entities\MapTranslation;
use Gedmo\Translatable\TranslatableListener;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Dravencms\Model\Locale\Entities\ILocale;

/**
 * Class MapTranslationRepository
 * @package Dravencms\Model\Map\Repository
 */
class MapTranslationRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $mapTranslationRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * CarouselRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->mapTranslationRepository = $entityManager->getRepository(MapTranslation::class);
    }

    /**
     * @param $id
     * @return mixed|null|MapTranslation
     */
    public function getOneById($id)
    {
        return $this->mapTranslationRepository->find($id);
    }

    /**
     * @param $id
     * @return MapTranslation[]
     */
    public function getById($id)
    {
        return $this->mapTranslationRepository->findBy(['id' => $id]);
    }


    /**
     * @param $name
     * @param ILocale $locale
     * @param Map|null $mapIgnore
     * @return boolean
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isNameFree($name, ILocale $locale, Map $mapIgnore = null)
    {
        $qb = $this->mapTranslationRepository->createQueryBuilder('mt')
            ->select('mt')
            ->join('mt.map', 'm')
            ->where('mt.name = :name')
            ->andWhere('mt.locale = :locale')
            ->setParameters([
                'name' => $name,
                'locale' => $locale
            ]);

        if ($mapIgnore)
        {
            $qb->andWhere('m != :mapIgnore')
                ->setParameter('mapIgnore', $mapIgnore);
        }

        $query = $qb->getQuery();
        return (is_null($query->getOneOrNullResult()));
    }

    /**
     * @param Map $map
     * @param ILocale $locale
     * @return null|MapTranslation
     */
    public function getTranslation(Map $map, ILocale $locale)
    {
        return $this->mapTranslationRepository->findOneBy(['map' => $map, 'locale' => $locale]);
    }
}