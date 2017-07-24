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
     * @param array $parameters
     * @return null|Map
     */
    public function getOneByParameters(array $parameters = [])
    {
        return $this->mapRepository->findOneBy($parameters);
    }
}