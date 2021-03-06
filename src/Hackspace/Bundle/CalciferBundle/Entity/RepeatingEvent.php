<?php

namespace Hackspace\Bundle\CalciferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use enko\RelativeDateParser\RelativeDateParser;

/**
 * RepeatEvent
 *
 * @property \DateTime $nextdate
 * @property integer $duration
 * @property string $repeating_pattern
 * @property string $summary
 * @property string $description
 * @property Location $location
 * @property string $url
 * @property array $tags
 *
 * @ORM\Table(name="repeating_events")
 * @ORM\Entity
 */
class RepeatingEvent extends BaseEntity
{
    use TagTrait;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="nextdate", type="datetimetz")
     */
    protected $nextdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    protected $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="repeating_pattern", type="string", length=255)
     */
    protected $repeating_pattern = '';

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255)
     */
    protected $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Location")
     * @ORM\JoinColumn(name="locations_id", referencedColumnName="id")
     */
    protected $location;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="repeating_events2tags",
     *      joinColumns={@ORM\JoinColumn(name="repeating_events_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tags_id", referencedColumnName="id")}
     *      )
     */
    protected $tags = [];

    public function isValid() {
        $errors = [];
        if ((is_null($this->nextdate)) && (strlen($this->nextdate) > 0)) {
            $errors['nextdate'] = "Bitte gebe einen nächsten Termin an.";
        }
        if ((is_null($this->repeating_pattern)) && (strlen($this->repeating_pattern) > 0)) {
            $errors['repeating_pattern'] = "Bitte gebe ein gültiges Wiederholungsmuster an.";
        } else {
            $this->nextdate->setTimezone(new \DateTimeZone('Europe/Berlin'));
            try {
                $parser = new RelativeDateParser($this->repeating_pattern,$this->nextdate,'de');
            } catch (\Exception $e) {
                $errors['repeating_pattern'] = "Bitte gebe ein gültiges Wiederholungsmuster an.";
            }
        }
        if ((is_null($this->summary)) && (strlen($this->summary) > 0)) {
            $errors['summary'] = "Bitte gebe eine Zusammenfassung an.";
        }
        return $errors;
    }
}