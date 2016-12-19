<?php

namespace DavidKmenta\CommandSupervisorBundle\Entity;

class CommandStatus
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int|null
     */
    private $threshold;

    /**
     * @var \DateTime|null
     */
    private $lastRun;

    /**
     * NULL means unknown status of the command
     * @var bool|null
     */
    private $status;

    /**
     * @param string $name
     * @param int|null $threshold
     * @param \DateTime|null $lastRun
     * @param bool|null $status
     */
    public function __construct($name, $threshold = null, \DateTime $lastRun = null, $status = null)
    {
        $this->name = $name;
        $this->threshold = $threshold;
        $this->lastRun = $lastRun;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getThreshold()
    {
        return $this->threshold;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastRun()
    {
        return $this->lastRun;
    }

    /**
     * @return bool|null
     */
    public function getStatus()
    {
        return $this->status;
    }
}
