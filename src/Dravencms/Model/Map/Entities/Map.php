<?php
namespace Dravencms\Model\Map\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dravencms\Model\Locale\Entities\ILocale;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class Map
 * @package App\Model\Map\Entities
 * @ORM\Entity
 * @ORM\Table(name="mapMap")
 */
class Map extends Nette\Object
{
    use Identifier;
    use TimestampableEntity;

    const TYPE_ROADMAP = 'ROADMAP';
    const TYPE_SATELLITE = 'SATELLITE';
    const TYPE_HYBRID = 'HYBRID';
    const TYPE_TERRAIN = 'TERRAIN';

    const HEIGHT_TYPE_PX = 'px';
    const HEIGHT_TYPE_PERCENT = '%';

    const WIDTH_TYPE_PX = 'px';
    const WIDTH_TYPE_PERCENT = '%';

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    private $identifier;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    private $apiKey;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    private $street;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    private $zipCode;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    private $city;


    /**
     * @var string
     * @ORM\Column(type="string",length=255)
     */
    private $type;

    /**
     * @var integer
     * @ORM\Column(type="integer",nullable=false)
     */
    private $zoom;

    /**
     * @var integer
     * @ORM\Column(type="integer",nullable=false)
     */
    private $height;

    /**
     * @var integer
     * @ORM\Column(type="integer",nullable=false)
     */
    private $width;

    /**
     * @var string
     * @ORM\Column(type="string",length=255)
     */
    private $heightType;

    /**
     * @var string
     * @ORM\Column(type="string",length=255)
     */
    private $widthType;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isShowName;

    /**
     * @var ArrayCollection|MapTranslation[]
     * @ORM\OneToMany(targetEntity="MapTranslation", mappedBy="map",cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * Map constructor.
     * @param $identifier
     * @param $apiKey
     * @param $street
     * @param $zipCode
     * @param $city
     * @param string $type
     * @param int $zoom
     * @param int $height
     * @param int $width
     * @param string $heightType
     * @param string $widthType
     * @param bool $isActive
     * @param bool $isShowName
     */
    public function __construct($identifier, $apiKey, $street, $zipCode, $city, $type = self::TYPE_SATELLITE, $zoom = 1, $height = 100, $width = 100, $heightType = self::HEIGHT_TYPE_PERCENT, $widthType = self::WIDTH_TYPE_PERCENT, $isActive = true, $isShowName = true)
    {
        $this->identifier = $identifier;
        $this->apiKey = $apiKey;
        $this->street = $street;
        $this->zipCode = $zipCode;
        $this->city = $city;
        $this->type = $type;
        $this->zoom = $zoom;
        $this->height = $height;
        $this->width = $width;
        $this->heightType = $heightType;
        $this->widthType = $widthType;
        $this->isActive = $isActive;
        $this->isShowName = $isShowName;
        $this->translations = new ArrayCollection();
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if (!in_array($type, [self::TYPE_HYBRID, self::TYPE_ROADMAP, self::TYPE_SATELLITE, self::TYPE_TERRAIN]))
        {
            throw new \InvalidArgumentException('$type is invalid');
        }
        $this->type = $type;
    }

    /**
     * @param int $zoom
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @param string $heightType
     */
    public function setHeightType($heightType)
    {
        $this->heightType = $heightType;
    }

    /**
     * @param string $widthType
     */
    public function setWidthType($widthType)
    {
        $this->widthType = $widthType;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @param boolean $isShowName
     */
    public function setIsShowName($isShowName)
    {
        $this->isShowName = $isShowName;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return string
     */
    public function getHeightType()
    {
        return $this->heightType;
    }

    /**
     * @return string
     */
    public function getWidthType()
    {
        return $this->widthType;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return boolean
     */
    public function isShowName()
    {
        return $this->isShowName;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return ArrayCollection|MapTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param ILocale $locale
     * @return MapTranslation
     */
    public function getTranslation(ILocale $locale)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq("locale", $locale));
        return $this->getTranslations()->matching($criteria)->first();
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}

