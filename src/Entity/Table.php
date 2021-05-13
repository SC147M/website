<?php

namespace App\Entity;

use App\Entity\Traits\TimestampAble;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TableRepository")
 * @ORM\Table(name="snooker_table")
 * @ORM\HasLifecycleCallbacks
 */
class Table
{
    use TimestampAble;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $locked;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Reservation", mappedBy="tables")
     */
    private $reservations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SnookerBreak", mappedBy="snookerTable", orphanRemoval=true)
     */
    private $snookerBreaks;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->snookerBreaks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->addTable($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            $reservation->removeTable($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Collection|SnookerBreak[]
     */
    public function getSnookerBreaks(): Collection
    {
        return $this->snookerBreaks;
    }

    public function addSnookerBreak(SnookerBreak $snookerBreak): self
    {
        if (!$this->snookerBreaks->contains($snookerBreak)) {
            $this->snookerBreaks[] = $snookerBreak;
            $snookerBreak->setSnookerTable($this);
        }

        return $this;
    }

    public function removeSnookerBreak(SnookerBreak $snookerBreak): self
    {
        if ($this->snookerBreaks->contains($snookerBreak)) {
            $this->snookerBreaks->removeElement($snookerBreak);
            // set the owning side to null (unless already changed)
            if ($snookerBreak->getSnookerTable() === $this) {
                $snookerBreak->setSnookerTable(null);
            }
        }

        return $this;
    }
}
