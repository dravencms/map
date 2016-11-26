<?php

namespace Dravencms\Map\Script;

use Dravencms\Model\Admin\Entities\Menu;
use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\User\Entities\AclOperation;
use Dravencms\Model\User\Entities\AclResource;
use Dravencms\Model\User\Repository\AclOperationRepository;
use Dravencms\Model\User\Repository\AclResourceRepository;
use Dravencms\Packager\IPackage;
use Dravencms\Packager\IScript;
use Kdyby\Doctrine\EntityManager;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class PostInstall implements IScript
{
    private $menuRepository;
    private $entityManager;
    private $aclResourceRepository;
    private $aclOperationRepository;

    public function __construct(MenuRepository $menuRepository, EntityManager $entityManager, AclOperationRepository $aclOperationRepository, AclResourceRepository $aclResourceRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
        $this->aclOperationRepository = $aclOperationRepository;
        $this->aclResourceRepository = $aclResourceRepository;
    }

    public function run(IPackage $package)
    {
        if (!$aclResource = $this->aclResourceRepository->getOneByName('map')) {
            $aclResource = new AclResource('map', 'Map');
            $this->entityManager->persist($aclResource);
        }

        if (!$aclOperationEdit = $this->aclOperationRepository->getOneByName('edit')) {
            $aclOperationEdit = new AclOperation($aclResource, 'edit', 'Allows editation of map');
            $this->entityManager->persist($aclOperationEdit);
        }

        if (!$aclOperationDelete = $this->aclOperationRepository->getOneByName('delete')) {
            $aclOperationDelete = new AclOperation($aclResource, 'delete', 'Allows deletion of map');
            $this->entityManager->persist($aclOperationDelete);
        }

        if (!$this->menuRepository->getOneByPresenter(':Admin:Map:Map')) {
            $adminMenu = new Menu('Maps', ':Admin:Map:Map', 'fa-map', $aclOperationEdit);

            $foundRoot = $this->menuRepository->getOneByName('Site items');

            if ($foundRoot) {
                $this->menuRepository->getMenuRepository()->persistAsLastChildOf($adminMenu, $foundRoot);
            } else {
                $this->entityManager->persist($adminMenu);
            }
        }

        $this->entityManager->flush();

    }
}