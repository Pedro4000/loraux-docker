<?php

namespace App\Entity;

use App\Repository\DiscogsVideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscogsVideoRepository::class)]
class DiscogsVideo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: 'discogsVideos')]
    private Collection $artists;

    #[ORM\ManyToOne(inversedBy: 'label')]
    private ?Release $release = null;

    #[ORM\ManyToOne(inversedBy: 'discogsVideos')]
    private ?Label $label = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastTimeFullyScrapped = null;

    public function __construct()
    {
        $this->artists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getArtists()
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): self
    {
        if (!$this->artists->contains($artist)) {
            $this->artists[] = $artist;
        }
        return $this;
    }
    public function removeArtist(Artist $artist): self
    {
        if ($this->artists->contains($artist)) {
            $this->artists->removeElement($artist);
        }
        return $this;
    }

    public function getRelease(): ?Release
    {
        return $this->release;
    }

    public function setRelease(?Release $release): self
    {
        $this->release = $release;

        return $this;
    }

    public function getLabel(): ?Label
    {
        return $this->label;
    }

    public function setLabel(?Label $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLastTimeFullyScrapped(): ?\DateTimeInterface
    {
        return $this->lastTimeFullyScrapped;
    }

    public function setLastTimeFullyScrapped(?\DateTimeInterface $lastTimeFullyScrapped): self
    {
        $this->lastTimeFullyScrapped = $lastTimeFullyScrapped;

        return $this;
    }
}
