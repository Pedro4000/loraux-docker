<?php

namespace App\Entity;

use App\Repository\ReleaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReleaseRepository::class)
 * @ORM\Table(name="`release`")
 */
class Release
{


    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->tracks = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $discogsId;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $videos = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Label", inversedBy="releases")
     */
    private $labels;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Artist", inversedBy="releases")
     */
    private $artists;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $releaseDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Track", mappedBy="release")
     */
    private $tracks;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscogsId(): ?int
    {
        return $this->discogsId;
    }

    public function setDiscogsId(int $discogsId): self
    {
        $this->discogsId = $discogsId;

        return $this;
    }

    public function getVideos(): ?array
    {
        return $this->videos;
    }

    public function setVideos(?array $videos): self
    {
        $this->videos = $videos;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabels()
    {
        return $this->labels;
    }

    public function addLabel(Label $label): self
    {
        if (!$this->labels->contains($label)) {
            $this->labels[] = $label;
            $label->addRelease($this);
        }
        return $this;
    }
    public function removeLabel(Label $label): self
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
            $label->removeRelease($this);
        }
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


    /**
     * @return mixed
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * @param mixed $releaseDate
     */
    public function setReleaseDate($releaseDate): void
    {
        $this->releaseDate = $releaseDate;
    }

    /**
     * @return mixed
     */
    public function getTracks()
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): self
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks[] = $track;
        }
        return $this;
    }

    public function removeTrack(Track $track): self
    {
        if ($this->tracks->contains($track)) {
            $this->tracks->removeElement($track);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }






}
