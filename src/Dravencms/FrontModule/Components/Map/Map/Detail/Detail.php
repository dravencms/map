<?php

namespace Dravencms\FrontModule\Components\Map\Map\Detail;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Locale\CurrentLocale;
use Dravencms\Locale\CurrentLocaleResolver;
use Dravencms\Model\Map\Repository\MapRepository;
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

    /** @var CurrentLocale */
    private $currentLocale;

    /**
     * Detail constructor.
     * @param ICmsActionOption $cmsActionOption
     * @param MapRepository $mapRepository
     * @param CurrentLocale $currentLocale
     */
    public function __construct(
        ICmsActionOption $cmsActionOption,
        MapRepository $mapRepository,
        CurrentLocaleResolver $currentLocaleResolver
    )
    {
        parent::__construct();

        $this->cmsActionOption = $cmsActionOption;
        $this->mapRepository = $mapRepository;
        $this->currentLocale = $currentLocaleResolver->getCurrentLocale();
    }

    public function render()
    {
        $template = $this->template;

        $template->map = $this->mapRepository->getOneById($this->cmsActionOption->getParameter('id'));
        $template->currentLocale = $this->currentLocale;

        $template->setFile(__DIR__.'/detail.latte');
        $template->render();
    }
}
