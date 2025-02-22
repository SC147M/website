<?php

namespace App\Entity;

use App\Entity\Traits\TimestampAble;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    use TimestampAble;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reservation", mappedBy="user", orphanRemoval=true)
     */
    private $reservations;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Reservation", mappedBy="participants")
     */
    private $participations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\News", mappedBy="user", orphanRemoval=true)
     */
    private $news;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hash;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Ranking", mappedBy="user", cascade={"persist", "remove"})
     */
    private $ranking;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Match", mappedBy="user1", orphanRemoval=true)
     */
    private $matches1;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Match", mappedBy="user2", orphanRemoval=true)
     */
    private $matches2;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SnookerBreak", mappedBy="user", orphanRemoval=true)
     */
    private $snookerBreaks;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->matches1 = new ArrayCollection();
        $this->matches2 = new ArrayCollection();
        $this->snookerBreaks = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            // guarantee every user at least has ROLE_PENDING
            $roles[] = 'ROLE_PENDING';
        }

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Reservation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->addParticipant($this);
        }

        return $this;
    }

    public function removeParticipation(Reservation $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            $participation->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|News[]
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news[] = $news;
            $news->setUser($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->contains($news)) {
            $this->news->removeElement($news);
            // set the owning side to null (unless already changed)
            if ($news->getUser() === $this) {
                $news->setUser(null);
            }
        }

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getRanking(): ?Ranking
    {
        return $this->ranking;
    }

    public function setRanking(Ranking $ranking): self
    {
        $this->ranking = $ranking;

        // set the owning side of the relation if necessary
        if ($ranking->getUser() !== $this) {
            $ranking->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Match[]
     */
    public function getMatches1(): Collection
    {
        return $this->matches1;
    }

    public function addMatch1(Match $match): self
    {
        if (!$this->matches1->contains($match)) {
            $this->matches1[] = $match;
            $match->setUser1($this);
        }

        return $this;
    }

    public function removeMatch1(Match $match): self
    {
        if ($this->matches1->contains($match)) {
            $this->matches1->removeElement($match);
            // set the owning side to null (unless already changed)
            if ($match->getUser1() === $this) {
                $match->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Match[]
     */
    public function getMatches2(): Collection
    {
        return $this->matches2;
    }

    public function addMatch2(Match $match): self
    {
        if (!$this->matches2->contains($match)) {
            $this->matches2[] = $match;
            $match->setUser2($this);
        }

        return $this;
    }

    public function removeMatch2(Match $match): self
    {
        if ($this->matches2->contains($match)) {
            $this->matches2->removeElement($match);
            // set the owning side to null (unless already changed)
            if ($match->getUser2() === $this) {
                $match->setUser2(null);
            }
        }

        return $this;
    }

    public function getShortName() {
        if ($this.getFirstName() == 'Martin') {
            return $this->getFirstName() . ' ' . substr($this->getLastName(), 0, 2) . '.';
        }
        return $this->getFirstName() . ' ' . substr($this->getLastName(), 0, 1) . '.';
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
            $snookerBreak->setUser($this);
        }

        return $this;
    }

    public function removeSnookerBreak(SnookerBreak $snookerBreak): self
    {
        if ($this->snookerBreaks->contains($snookerBreak)) {
            $this->snookerBreaks->removeElement($snookerBreak);
            // set the owning side to null (unless already changed)
            if ($snookerBreak->getUser() === $this) {
                $snookerBreak->setUser(null);
            }
        }

        return $this;
    }
}
