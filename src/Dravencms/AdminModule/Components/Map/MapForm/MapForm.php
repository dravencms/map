<?php
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
use Kdyby\Doctrine\EntityManager;
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
        Map $map = null
    ) {
        parent::__construct();

        $this->map = $map;

        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->mapRepository = $mapRepository;
        $this->mapTranslationRepository = $mapTranslationRepository;
        $this->localeRepository = $localeRepository;


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
                'isShowName' => $this->map->isShowName()
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

        $this['form']->setDefaults($defaults);
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
                ->setRequired('Please enter map name.')
                ->addRule(Form::MAX_LENGTH, 'Map name is too long.', 255);

            $container->addText('title')
                ->setRequired('Please enter map title.')
                ->addRule(Form::MAX_LENGTH, 'Map title is too long.', 255);
        }

        $form->addText('apiKey')
            ->setRequired('Please enter google map api key.')
            ->addRule(Form::MAX_LENGTH, 'Api key is too long.', 255);

        $form->addText('street')
            ->setRequired('Please enter map street.')
            ->addRule(Form::MAX_LENGTH, 'Street name is too long.', 255);

        $form->addText('zipCode')
            ->setRequired('Please enter map Zip Code.')
            ->addRule(Form::MAX_LENGTH, 'Zip Code is too long.', 255);

        $form->addText('city')
            ->setRequired('Please enter map City.')
            ->addRule(Form::MAX_LENGTH, 'City is too long.', 255);


        $types = array();
        $types[Map::TYPE_ROADMAP] = 'Roadmap';
        $types[Map::TYPE_SATELLITE] = 'Satellite';
        $types[Map::TYPE_HYBRID] = 'Satellite with street names';
        $types[Map::TYPE_TERRAIN] = 'Terrain';
        $form->addSelect('type', 'Type:', $types)
            ->setRequired('Please enter map type.')
            ->setAttribute('class', 'form-control');


        $zooms = [];
        $zooms[0] = 'Fully Zoomed out';
        $zooms[1] = '4000 km';
        $zooms[2] = '2000 km (world)';
        $zooms[3] = '1000 km';
        $zooms[4] = '400 km (continent)';
        $zooms[5] = '200 km';
        $zooms[6] = '100 km';
        $zooms[7] = '50 km';
        $zooms[8] = '30 km';
        $zooms[9] = '15 km (area)';
        $zooms[10] = '8 km';
        $zooms[11] = '4 km';
        $zooms[12] = '2 km (city)';
        $zooms[13] = '1 km';
        $zooms[14] = '400 m (district)';
        $zooms[15] = '200 m';
        $zooms[16] = '100 m';
        $zooms[17] = '50 m (street)';
        $zooms[18] = '20 m';
        $zooms[19] = '10 m';
        $zooms[20] = '5 m (house)';
        $zooms[20] = '2.5 m';
        $form->addSelect('zoom', null, $zooms)
            ->setRequired('Please enter map zoom.');

        $form->addText('width')
            ->setRequired('Please enter map width.');

        $form->addText('height')
            ->setRequired('Please enter map height.');

        $form->addText('identifier')
            ->setRequired('Please enter map identifier.');

        $widthType = [];
        $widthType[Map::WIDTH_TYPE_PX] = 'Pixels';
        $widthType[Map::WIDTH_TYPE_PERCENT] = 'Percent';
        $form->addSelect('widthType', null, $widthType)
            ->setRequired('Please enter map width type.');

        $heightType = [];
        $heightType[Map::HEIGHT_TYPE_PX] = 'Pixels';
        $heightType[Map::HEIGHT_TYPE_PERCENT] = 'Percent';
        $form->addSelect('heightType', null, $heightType)
            ->setRequired('Please enter map height type.');

        $form->addCheckbox('isActive');
        $form->addCheckbox('isShowName');


        $form->addSubmit('send');

        $form->onValidate[] = [$this, 'editFormValidate'];
        $form->onSuccess[] = [$this, 'editFormSucceeded'];

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
                $form->addError('Tento název je již zabrán.');
            }
        }

        if (!$this->presenter->isAllowed('map', 'edit')) {
            $form->addError('Nemáte oprávění editovat map.');
        }
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function editFormSucceeded(Form $form)
    {
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