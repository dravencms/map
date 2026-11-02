<?php declare(strict_types=1);

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\MapModule;


use Dravencms\AdminModule\Components\Map\MapForm\MapFormFactory;
use Dravencms\AdminModule\Components\Map\MapGrid\MapGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Map\Entities\Map;
use Dravencms\Model\Map\Repository\MapRepository;

/**
 * Description of ArticlePresenter
 *
 * @author sadam
 */
class MapPresenter extends SecuredPresenter
{
    /** @var MapRepository @inject */
    public $mapRepository;

    /** @var MapFormFactory @inject */
    public $mapFormFactory;

    /** @var MapGridFactory @inject */
    public $mapGridFactory;
    
    /** @var null|Map */
    private $map = null;

    #[\Dravencms\User\Attributes\IsAllowed('map', 'edit')]
    public function renderDefault()
    {
        $this->template->h1 = $this->translator->translate('map.admin.title.maps');
    }

    /**
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    #[\Dravencms\User\Attributes\IsAllowed('map', 'edit')]
    public function actionEdit($id)
    {
        if ($id) {
            $map = $this->mapRepository->getOneById($id);

            if (!$map) {
                $this->error();
            }

            $this->map = $map;

            $this->template->h1 = $this->translator->translate('map.admin.title.editMap', ['identifier' => $map->getIdentifier()]);
        } else {
            $this->template->h1 = $this->translator->translate('map.admin.title.newMap');
        }
    }

    /**
     * @return \Dravencms\AdminModule\Components\Map\MapForm\MapForm
     */
    protected function createComponentFormMap()
    {
        $control = $this->mapFormFactory->create($this->map);
        $control->onSuccess[] = function()
        {
            $this->flashMessage($this->translator->translate('map.admin.flash.saved'), 'alert-success');
            $this->redirect('Map:');
        };
        return $control;
    }

    /**
     * @return \Dravencms\AdminModule\Components\Map\MapGrid\MapGrid
     */
    public function createComponentGridMap()
    {
        $control = $this->mapGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage($this->translator->translate('map.admin.flash.deleted'), 'alert-success');
            $this->redirect('Map:');
        };
        return $control;
    }
}
