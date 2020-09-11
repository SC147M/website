<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\News", inversedBy="tags")
     */
    private $News;

    public function __construct()
    {
        $this->News = new ArrayCollection();
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

    /**
     * @return Collection|News[]
     */
    public function getNews(): Collection
    {
        return $this->News;
    }

    public function addNews(News $news): self
    {
        if (!$this->News->contains($news)) {
            $this->News[] = $news;
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->News->contains($news)) {
            $this->News->removeElement($news);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
