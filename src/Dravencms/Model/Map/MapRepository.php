<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace App\Model\Map\Repository;

use App\Model\BaseRepository;
use App\Model\Carousel\Entities\Carousel;
use App\Model\Map\Entities\Map;
use Gedmo\Translatable\TranslatableListener;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Salamek\Cms\CmsActionOption;
use Salamek\Cms\ICmsActionOption;
use Salamek\Cms\ICmsComponentRepository;
use Salamek\Cms\Models\ILocale;

/**
 * Class MapRepository
 * @package App\Model\Map\Repository
 */
class MapRepository extends BaseRepository implements ICmsComponentRepository
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


    /**
     * @param string $componentAction
     * @return ICmsActionOption[]
     */
    public function getActionOptions($componentAction)
    {
        switch ($componentAction)
        {
            case 'Detail':
                $return = [];
                /** @var Map $map */
                foreach ($this->mapRepository->findBy(['isActive' => true]) AS $map) {
                    $return[] = new CmsActionOption($map->getName(), ['id' => $map->getId()]);
                }
                break;

            default:
                return false;
                break;
        }


        return $return;
    }

    /**
     * @param string $componentAction
     * @param array $parameters
     * @param ILocale $locale
     * @return null|CmsActionOption
     */
    public function getActionOption($componentAction, array $parameters, ILocale $locale)
    {
        $found = $this->findTranslatedOneBy($this->mapRepository, $locale, $parameters + ['isActive' => true]);

        if ($found)
        {
            return new CmsActionOption($found->getName(), $parameters);
        }

        return null;
    }
}