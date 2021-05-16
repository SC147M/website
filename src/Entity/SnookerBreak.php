<?php

namespace App\Entity;

use App\Entity\Traits\TimestampAble;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SnookerBreakRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SnookerBreak
{
    use TimestampAble;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="snookerBreaks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="smallint")
     */
    private $score;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Table", inversedBy="snookerBreaks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $snookerTable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $opponent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getSnookerTable(): ?Table
    {
        return $this->snookerTable;
    }

    public function setSnookerTable(?Table $snookerTable): self
    {
        $this->snookerTable = $snookerTable;

        return $this;
    }

    public function getOpponent(): ?User
    {
        return $this->opponent;
    }

    public function setOpponent(?User $opponent): self
    {
        $this->opponent = $opponent;

        return $this;
    }

    public function __toString()
    {
       return '';
    }
}
