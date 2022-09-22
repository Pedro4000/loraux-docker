<?php

namespace App\Entity;

use App\Repository\ReleaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ReleaseRepository::class)]
#[ORM\Table(name: 'release')]
class Release extends DiscogsClass
{
    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->tracks = new ArrayCollection();
        $this->labels = new ArrayCollection();
        $this->discogsVideos = new ArrayCollection();
    }

    #[ORM\ManyToMany(targetEntity: Label::class, mappedBy:"releases")]
    private $labels;

    #[ORM\ManyToMany(targetEntity: Artist::class, mappedBy:"releases")]
    private $artists;

    #[ORM\ManyToMany(targetEntity: Track::class, mappedBy:"releases")]
    private $tracks;

    #[ORM\OneToMany(mappedBy: 'release', targetEntity: DiscogsVideo::class)]
    private Collection $discogsVideos;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $releaseDate = null;


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
    public function getDiscogsVideos()
    {
        return $this->discogsVideos;
    }

    public function addDiscogsVideo(DiscogsVideo $discogsVideo): self
    {
        if (!$this->discogsVideos->contains($discogsVideo)) {
            $this->discogsVideos[] = $discogsVideo;
            $discogsVideo->setRelease($this);
        }
        return $this;
    }
    public function removeDiscogsVideo(DiscogsVideo $discogsVideo): self
    {
        if ($this->discogsVideos->contains($discogsVideo)) {
            $this->discogsVideos->removeElement($discogsVideo);
            $discogsVideo->setRelease(new ArrayCollection());
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

    // TODO AJOUTER GETTER SETTERS POUR LA DERNIERE RELATION

}
