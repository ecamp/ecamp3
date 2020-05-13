<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * ActivityType.
 *
 * @ORM\Entity
 */
class ActivityType extends BaseEntity {
    const TEMPLATE_GENERAL = 'General';

    /**
     * @ORM\ManyToMany(targetEntity="CampType", mappedBy="activityTypes")
     */
    protected $campTypes;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ActivityTypeContentType", mappedBy="activityType", orphanRemoval=true)
     */
    protected $activityTypeContentTypes;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ActivityTypeFactory", mappedBy="activityType")
     */
    protected $activityTypeFactories;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $template = self::TEMPLATE_GENERAL;

    /**
     * @var string
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private $defaultColor = '#1fa2df';

    /**
     * @var string
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $defaultNumberingStyle;

    public function __construct() {
        parent::__construct();

        $this->campTypes = new ArrayCollection();
        $this->activityTypeContentTypes = new ArrayCollection();
        $this->activityTypeFactories = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTemplate() {
        return $this->template;
    }

    public function setTemplate($template) {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getDefaultColor() {
        return $this->defaultColor;
    }

    public function setDefaultColor($defaultColor) {
        $this->defaultColor = $defaultColor;
    }

    /**
     * @return string
     */
    public function getDefaultNumberingStyle() {
        return $this->defaultNumberingStyle;
    }

    public function setDefaultNumberingStyle($defaultNumberingStyle) {
        $this->defaultNumberingStyle = $defaultNumberingStyle;
    }

    /**
     * @return ArrayCollection
     */
    public function getActivityTypeContentTypes() {
        return $this->activityTypeContentTypes;
    }

    public function addActivityTypeContentType(ActivityTypeContentType $activityTypeContentType) {
        $activityTypeContentType->setActivityType($this);
        $this->activityTypeContentTypes->add($activityTypeContentType);
    }

    public function removeActivityTypeContentType(ActivityTypeContentType $activityTypeContentType) {
        $activityTypeContentType->setActivityType(null);
        $this->activityTypeContentTypes->removeElement($activityTypeContentType);
    }

    /**
     * @return ArrayCollection
     */
    public function getActivityTypeFactories() {
        return $this->activityTypeFactories;
    }

    public function addActivityTypeFactory(ActivityTypeFactory $factory) {
        $factory->setActivityType($this);
        $this->activityTypeFactories->add($factory);
    }

    public function removeActivityTypeFactory(ActivityTypeFactory $factory) {
        $factory->setActivityType(null);
        $this->activityTypeFactories->removeElement($factory);
    }
}
