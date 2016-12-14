<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Map\Repository;

use Dravencms\Locale\TLocalizedRepository;
use Dravencms\Model\Map\Entities\Map;
use Gedmo\Translatable\TranslatableListener;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Dravencms\Model\Locale\Entities\ILocale;

/**
 * Class MapRepository
 * @package App\Model\Map\Repository
 */
class MapRepository
{
    use TLocalizedRepository;

    /** @var \Kdyby\Doctrine\EntityRepository */
    private $mapRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * CarouselRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->mapRepository = $entityManager->getRepository(Map::class);
    }

    /**
     * @param $id
     * @return mixed|null|Map
     */
    public function getOneById($id)
    {
        return $this->mapRepository->find($id);
    }

    /**
     * @param $id
     * @return Map[]
     */
    public function getById($id)
    {
        return $this->mapRepository->findBy(['id' => $id]);
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getMapQueryBuilder()
    {
        $qb = $this->mapRepository->createQueryBuilder('m')
            ->select('m');
        return $qb;
    }

    /**
     * @return Map[]
     */
    public function getActive()
    {
        return $this->mapRepository->findBy(['isActive' => true]);
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
        $qb = $this->mapRepository->createQueryBuilder('m')
            ->select('m')
            ->where('m.name = :name')
            ->setParameters([
                'name' => $name
            ]);

        if ($mapIgnore)
        {
            $qb->andWhere('m != :mapIgnore')
                ->setParameter('mapIgnore', $mapIgnore);
        }

        $query = $qb->getQuery();
        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale->getLanguageCode());

        return (is_null($query->getOneOrNullResult()));
    }
}