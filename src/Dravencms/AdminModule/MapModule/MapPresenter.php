<?php

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

    /**
     * @isAllowed(map,edit)
     */
    public function renderDefault()
    {
        $this->template->h1 = 'Maps';
    }

    /**
     * @isAllowed(map,edit)
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit($id)
    {
        if ($id) {
            $map = $this->mapRepository->getOneById($id);

            if (!$map) {
                $this->error();
            }

            $this->map = $map;

            $this->template->h1 = sprintf('Edit map „%s“', $map->getName());
        } else {
            $this->template->h1 = 'New map';
        }
    }

    /**
     * @return \AdminModule\Components\Map\MapForm
     */
    protected function createComponentFormMap()
    {
        $control = $this->mapFormFactory->create($this->map);
        $control->onSuccess[] = function()
        {
            $this->flashMessage('Map has been successfully saved', 'alert-success');
            $this->redirect('Map:');
        };
        return $control;
    }

    /**
     * @return \AdminModule\Components\Map\MapGrid
     */
    public function createComponentGridMap()
    {
        $control = $this->mapGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Map has been successfully deleted', 'alert-success');
            $this->redirect('Map:');
        };
        return $control;
    }
}
