<?php declare(strict_types=1);
/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Dravencms\AdminModule\Components\Map\MapForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Locale\Repository\LocaleRepository;
use Dravencms\Model\Map\Entities\Map;
use Dravencms\Model\Map\Entities\MapTranslation;
use Dravencms\Model\Map\Repository\MapRepository;
use Dravencms\Model\Map\Repository\MapTranslationRepository;
use Dravencms\Database\EntityManager;
use Nette\Application\UI\Form;

/**
 * Description of MapForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class MapForm extends BaseControl
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var MapRepository */
    private $mapRepository;

    /** @var LocaleRepository */
    private $localeRepository;

    /** @var MapTranslationRepository */
    private $mapTranslationRepository;

    /** @var Map|null */
    private $map = null;

    /** @var array */
    public $onSuccess = [];

    /**
     * MapForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param EntityManager $entityManager
     * @param MapRepository $mapRepository
     * @param MapTranslationRepository $mapTranslationRepository
     * @param LocaleRepository $localeRepository
     * @param Map|null $map
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        EntityManager $entityManager,
        MapRepository $mapRepository,
        MapTranslationRepository $mapTranslationRepository,
        LocaleRepository $localeRepository,
        ?Map $map = null
    ) {
        parent::__construct();

        $this->map = $map;

        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->mapRepository = $mapRepository;
        $this->mapTranslationRepository = $mapTranslationRepository;
        $this->localeRepository = $localeRepository;
    }

    /**
     * @return \Dravencms\Components\BaseForm\BaseForm
     */
    protected function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            $container = $form->addContainer($activeLocale->getLanguageCode());

            $container->addText('name')
                ->setRequired('map.admin.validation.nameRequired')
                ->addRule(Form::MAX_LENGTH, 'map.admin.validation.nameTooLong', 255);

            $container->addText('title')
                ->setRequired('map.admin.validation.titleRequired')
                ->addRule(Form::MAX_LENGTH, 'map.admin.validation.titleTooLong', 255);
        }

        $form->addText('apiKey')
            ->setRequired('map.admin.validation.apiKeyRequired')
            ->addRule(Form::MAX_LENGTH, 'map.admin.validation.apiKeyTooLong', 255);

        $form->addText('street')
            ->setRequired('map.admin.validation.streetRequired')
            ->addRule(Form::MAX_LENGTH, 'map.admin.validation.streetTooLong', 255);

        $form->addText('zipCode')
            ->setRequired('map.admin.validation.zipCodeRequired')
            ->addRule(Form::MAX_LENGTH, 'map.admin.validation.zipCodeTooLong', 255);

        $form->addText('city')
            ->setRequired('map.admin.validation.cityRequired')
            ->addRule(Form::MAX_LENGTH, 'map.admin.validation.cityTooLong', 255);


        $types = array();
        $types[Map::TYPE_ROADMAP] = 'map.admin.option.roadmap';
        $types[Map::TYPE_SATELLITE] = 'map.admin.option.satellite';
        $types[Map::TYPE_HYBRID] = 'map.admin.option.hybrid';
        $types[Map::TYPE_TERRAIN] = 'map.admin.option.terrain';
        $form->addSelect('type', 'map.admin.field.type', $types)
            ->setRequired('map.admin.validation.typeRequired')
            ->setHtmlAttribute('class', 'form-control');


        $zooms = [];
        $zooms[0] = 'map.admin.zoom.fullyOut';
        $zooms[1] = '4000 km';
        $zooms[2] = 'map.admin.zoom.world';
        $zooms[3] = '1000 km';
        $zooms[4] = 'map.admin.zoom.continent';
        $zooms[5] = '200 km';
        $zooms[6] = '100 km';
        $zooms[7] = '50 km';
        $zooms[8] = '30 km';
        $zooms[9] = 'map.admin.zoom.area';
        $zooms[10] = '8 km';
        $zooms[11] = '4 km';
        $zooms[12] = 'map.admin.zoom.city';
        $zooms[13] = '1 km';
        $zooms[14] = 'map.admin.zoom.district';
        $zooms[15] = '200 m';
        $zooms[16] = '100 m';
        $zooms[17] = 'map.admin.zoom.street';
        $zooms[18] = '20 m';
        $zooms[19] = '10 m';
        $zooms[20] = 'map.admin.zoom.house';
        $zooms[20] = '2.5 m';
        $form->addSelect('zoom', null, $zooms)
            ->setRequired('map.admin.validation.zoomRequired');

        $form->addText('width')
            ->setRequired('map.admin.validation.widthRequired');

        $form->addText('height')
            ->setRequired('map.admin.validation.heightRequired');

        $form->addText('identifier')
            ->setRequired('map.admin.validation.identifierRequired');

        $widthType = [];
        $widthType[Map::WIDTH_TYPE_PX] = 'map.admin.option.pixels';
        $widthType[Map::WIDTH_TYPE_PERCENT] = 'map.admin.option.percent';
        $form->addSelect('widthType', null, $widthType)
            ->setRequired('map.admin.validation.widthTypeRequired');

        $heightType = [];
        $heightType[Map::HEIGHT_TYPE_PX] = 'map.admin.option.pixels';
        $heightType[Map::HEIGHT_TYPE_PERCENT] = 'map.admin.option.percent';
        $form->addSelect('heightType', null, $heightType)
            ->setRequired('map.admin.validation.heightTypeRequired');

        $form->addCheckbox('isActive');
        $form->addCheckbox('isShowName');


        $form->addSubmit('send', 'map.admin.action.save');

        $form->onValidate[] = [$this, 'editFormValidate'];
        $form->onSuccess[] = [$this, 'editFormSucceeded'];

        if ($this->map) {
            $defaults = [
                'apiKey' => $this->map->getApiKey(),
                'street' => $this->map->getStreet(),
                'zipCode' => $this->map->getZipCode(),
                'city' => $this->map->getCity(),
                'type' => $this->map->getType(),
                'zoom' => $this->map->getZoom(),
                'width' => $this->map->getWidth(),
                'height' => $this->map->getHeight(),
                'widthType' => $this->map->getWidthType(),
                'heightType' => $this->map->getHeightType(),
                'isActive' => $this->map->isActive(),
                'isShowName' => $this->map->isShowName(),
                'identifier' => $this->map->getIdentifier()
            ];

            foreach ($this->map->getTranslations() AS $translation)
            {
                $defaults[$translation->getLocale()->getLanguageCode()]['name'] = $translation->getName();
                $defaults[$translation->getLocale()->getLanguageCode()]['title'] = $translation->getTitle();
            }
        }
        else{
            $defaults = [
                'isActive' => true,
                'isShowName' => true
            ];
        }

        $form->setDefaults($defaults);

        return $form;
    }

    /**
     * @param Form $form
     */
    public function editFormValidate(Form $form)
    {
        $values = $form->getValues();

        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            if (!$this->mapTranslationRepository->isNameFree($values->{$activeLocale->getLanguageCode()}->name, $activeLocale, $this->map)) {
                $form->addError('map.admin.validation.nameTaken');
            }
        }

        if (!$this->getPresenter()->isAllowed('map', 'edit')) {
            $form->addError('map.admin.validation.editForbidden');
        }
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function editFormSucceeded(Form $form)
    {
        $this->requirePermission('map', 'edit');
        $values = $form->getValues();
        
        if ($this->map) {
            $map = $this->map;
            $map->setIdentifier($values->identifier);
            $map->setApiKey($values->apiKey);
            $map->setStreet($values->street);
            $map->setZipCode($values->zipCode);
            $map->setCity($values->city);
            $map->setType($values->type);
            $map->setZoom($values->zoom);
            $map->setHeight($values->height);
            $map->setWidth($values->width);
            $map->setHeightType($values->heightType);
            $map->setWidthType($values->widthType);
            $map->setWidthType($values->widthType);
            $map->setIsActive($values->isActive);
            $map->setIsShowName($values->isShowName);
        } else {
            $map = new Map($values->identifier, $values->apiKey, $values->street, $values->zipCode, $values->city, $values->type, $values->zoom, $values->height, $values->width, $values->heightType, $values->widthType, $values->isActive, $values->isShowName);
        }
        
        $this->entityManager->persist($map);

        $this->entityManager->flush();

        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            if ($mapTranslation = $this->mapTranslationRepository->getTranslation($map, $activeLocale))
            {
                $mapTranslation->setName($values->{$activeLocale->getLanguageCode()}->name);
                $mapTranslation->setTitle($values->{$activeLocale->getLanguageCode()}->title);
            }
            else
            {
                $mapTranslation = new MapTranslation(
                    $map,
                    $activeLocale,
                    $values->{$activeLocale->getLanguageCode()}->name,
                    $values->{$activeLocale->getLanguageCode()}->title
                );
            }
            $this->entityManager->persist($mapTranslation);
        }
        $this->entityManager->flush();

        $this->onSuccess();
    }


    public function render()
    {
        $template = $this->template;
        $template->activeLocales = $this->localeRepository->getActive();
        $template->setFile(__DIR__ . '/MapForm.latte');
        $template->render();
    }
}
