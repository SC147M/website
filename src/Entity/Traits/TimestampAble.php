<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Adds created at and updated at timestamps to entities.
 * Entities using this must have HasLifecycleCallbacks annotation.
 *
 * @ORM\HasLifecycleCallbacks
 */
trait TimestampAble
{
    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * Gets triggered only on insert
     *
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * Gets triggered every time on update
     *
     * @ORM\PreUpdate
     * @throws \Exception
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new DateTime();
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     * @return TimestampAble
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param DateTime $updatedAt
     * @return TimestampAble
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}