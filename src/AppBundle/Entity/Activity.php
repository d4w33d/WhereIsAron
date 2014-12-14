<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Utility;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActivityRepository")
 * @ORM\Table(name="activity", indexes={@ORM\Index(name="activity_begins_at_idx", columns={"begins_at"})})
 */
class Activity
{

    /**
     * Returns valid email addresses and phone numbers from contact string
     *
     * @param string $str
     * @return array
     */
    public static function getContactsFromString($str)
    {
        $contacts = array();

        foreach (preg_split('/[\r\n]+/', trim($str)) as $line) {
            // Line is empty
            if (!($line = trim($line))) {
                continue;
            }

            // Check for phone or email format
            if (Utility::isEmailAddress($line)
                || Utility::isPhoneNumber($line)) {
                $contacts[] = $line;
            }
        }

        return $contacts;
    }

    /**
     * Sanitize contacts string, keeping only valid email addresses
     * and phone numbers
     *
     * @param string $str
     * @return string
     */
    public static function sanitizeContacts($str)
    {
        return implode("\n", self::getContactsFromString($str)) . "\n";
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $id_user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    protected $locations;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    protected $contacts;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $begins_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $ends_at;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(0)
     */
    protected $ping_delay;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $last_ping_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $ping_limit_at;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $alerted = false;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id_user
     *
     * @param integer $idUser
     * @return Activity
     */
    public function setIdUser($idUser)
    {
        $this->id_user = $idUser;

        return $this;
    }

    /**
     * Get id_user
     *
     * @return integer 
     */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Activity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Activity
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set locations
     *
     * @param string $locations
     * @return Activity
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;

        return $this;
    }

    /**
     * Get locations
     *
     * @return string 
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Set contacts
     *
     * @param string $contacts
     * @return Activity
     */
    public function setContacts($contacts)
    {
        $this->contacts = self::sanitizeContacts($contacts);

        return $this;
    }

    /**
     * Get contacts (if $asArray is TRUE, sanitize string and returns contacts
     * splitted into an array)
     *
     * @param boolean $asArray
     * @return mixed 
     */
    public function getContacts($asArray = false)
    {
        if ($asArray) {
            return self::getContactsFromString($this->contacts);
        }

        return $this->contacts;
    }

    /**
     * Set begins_at
     *
     * @param \DateTime $beginsAt
     * @return Activity
     */
    public function setBeginsAt($beginsAt)
    {
        $this->begins_at = $beginsAt;

        return $this;
    }

    /**
     * Get begins_at
     *
     * @return \DateTime 
     */
    public function getBeginsAt()
    {
        return $this->begins_at;
    }

    /**
     * Set ends_at
     *
     * @param \DateTime $endsAt
     * @return Activity
     */
    public function setEndsAt($endsAt)
    {
        $this->ends_at = $endsAt;

        return $this;
    }

    /**
     * Get ends_at
     *
     * @return \DateTime 
     */
    public function getEndsAt()
    {
        return $this->ends_at;
    }

    /**
     * Set ping_delay
     *
     * @param integer $pingDelay
     * @return Activity
     */
    public function setPingDelay($pingDelay)
    {
        $this->ping_delay = $pingDelay;

        return $this;
    }

    /**
     * Get ping_delay
     *
     * @return integer 
     */
    public function getPingDelay()
    {
        return $this->ping_delay;
    }

    /**
     * Set last_ping_at
     *
     * @param \DateTime $lastPingAt
     * @return Activity
     */
    public function setLastPingAt($lastPingAt)
    {
        $this->last_ping_at = $lastPingAt;

        $interval = new \DateInterval('PT' . $this->ping_delay . 'S');
        $this->ping_limit_at = clone $this->last_ping_at;
        $this->ping_limit_at->add($interval);

        return $this;
    }

    /**
     * Get last_ping_at
     *
     * @return \DateTime 
     */
    public function getLastPingAt()
    {
        return $this->last_ping_at;
    }

    /**
     * Get time since the last_ping_at
     *
     * @return integer
     */
    public function getLastPingSince()
    {
        return time() - $this->last_ping_at->getTimestamp();
    }

    /**
     * Set ping_limit_at
     *
     * @param \DateTime $pingDelay
     * @return Activity
     */
    public function setPingLimitAt($pingDelay)
    {
        $this->ping_limit_at = $pingDelay;

        return $this;
    }

    /**
     * Get ping_limit_at
     *
     * @return \DateTime 
     */
    public function getPingLimitAt()
    {
        return $this->ping_limit_at;
    }

    /**
     * Set alerted
     *
     * @param boolean $alerted
     * @return Activity
     */
    public function setAlerted($alerted)
    {
        $this->alerted = $alerted;

        return $this;
    }

    /**
     * Get alerted
     *
     * @return boolean 
     */
    public function getAlerted()
    {
        return $this->alerted;
    }

}
