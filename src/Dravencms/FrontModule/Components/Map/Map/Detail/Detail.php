<?php

namespace Dravencms\FrontModule\Components\Map\Map;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Dravencms\Components\BaseControl;
use App\Model\Map\Repository\MapRepository;
use Salamek\Cms\ICmsActionOption;

/**
 * Description of ShopPresenter
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class Detail extends BaseControl
{
    /** @var ICmsActionOption */
    private $cmsActionOption;

    /** @var MapRepository */
    private $mapRepository;

    public function __construct(ICmsActionOption $cmsActionOption, MapRepository $mapRepository)
    {
        parent::__construct();

        $this->cmsActionOption = $cmsActionOption;
        $this->mapRepository = $mapRepository;
    }

    public function render()
    {
        $template = $this->template;

        $template->map = $this->mapRepository->getOneById($this->cmsActionOption->getParameter('id'));

        $template->setFile(__DIR__.'/detail.latte');
        $template->render();
    }
}
