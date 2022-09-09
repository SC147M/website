<?php

namespace App\Entity;

use App\Entity\Traits\TimestampAble;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     repositoryMethod="getReservationsForValidate",
 *     fields={"id","start", "end", "tables"},
 *     errorPath="tables",
 *     message="table_already_in_use"
 * )
 * @UniqueEntity(
 *     repositoryMethod="getReservationsForValidate",
 *     fields={"start", "end", "tables"},
 *     errorPath="tables",
 *     message="table_already_in_use",
 *     groups={"create"}
 * )
 */
class Reservation
{
    const TYPE_TABLE = 1;
    const TYPE_LEAGUE_1 = 2;
    const TYPE_LEAGUE_2 = 3;
    const TYPE_TOURNEY = 4;
    const TYPE_OTHERS = 5;
    const TYPE_LEAGUE_3 = 6;

    use TimestampAble;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Table", inversedBy="reservations")
     */
    private $tables;

    /**
     *
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="participations")
     */
    private $participants;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * Reservation constructor.
     */
    public function __construct()
    {
        $this->tables = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->start = new \DateTime();
        $this->end = new \DateTime();
    }

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

    /**
     * @return Collection|Table[]
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Table $table): self
    {
        if (!$this->tables->contains($table)) {
            $this->tables[] = $table;
        }

        return $this;
    }

    public function removeTable(Table $table): self
    {
        if ($this->tables->contains($table)) {
            $this->tables->removeElement($table);
        }

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->start->format('d.M.Y');
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}