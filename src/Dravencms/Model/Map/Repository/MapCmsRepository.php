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
use Salamek\Cms\CmsActionOption;
use Salamek\Cms\ICmsActionOption;
use Salamek\Cms\ICmsComponentRepository;
use Salamek\Cms\Models\ILocale;

/**
 * Class MapRepository
 * @package App\Model\Map\Repository
 */
class MapCmsRepository implements ICmsComponentRepository
{
    /** @var MapRepository */
    private $mapRepository;

    public function __construct(MapRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
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
                foreach ($this->mapRepository->getActive() AS $map) {
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
     * @return null|CmsActionOption
     */
    public function getActionOption($componentAction, array $parameters)
    {
        $found = $this->mapRepository->getOneByParameters($parameters + ['isActive' => true]);
        
        if ($found)
        {
            $cmsActionOption =  new CmsActionOption($found->getIdentifier(), $parameters);
            return $cmsActionOption;
        }

        return null;
    }
}