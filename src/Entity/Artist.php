<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
#[ORM\Table(name: 'artist')]
class Artist extends DiscogsClass
{

    public function __construct(
        public ArrayCollection $releases,
        public ArrayCollection $tracks,
        public ArrayCollection $discogsVideos,
    )
    {}

    #[ORM\ManyToMany(targetEntity:Release::class, mappedBy:"artists")]
    private Collection $releases;

    #[ORM\ManyToMany(targetEntity:Track::class, mappedBy:"artists")]
    private Collection $tracks;

    #[ORM\ManyToMany(targetEntity: DiscogsVideo::class, mappedBy: 'artist')]
    private Collection $discogsVideos;


    /**
     * @return mixed
     */
    public function getReleases()
    {
        return $this->releases;
    }

    public function addRelease(Release $release): self
    {
        if (!$this->releases->contains($release)) {
            $this->releases[] = $release;
        }
        return $this;
    }
    public function removeRelease(Release $release): self
    {
        if ($this->releases->contains($release)) {
            $this->releases->removeElement($release);
        }
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTracks(): ArrayCollection
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
     * @return Collection<int, DiscogsVideo>
     */
    public function getDiscogsVideos(): Collection
    {
        return $this->discogsVideos;
    }

    public function addDiscogsVideo(DiscogsVideo $discogsVideo): self
    {
        if (!$this->discogsVideos->contains($discogsVideo)) {
            $this->discogsVideos->add($discogsVideo);
            $discogsVideo->addArtist($this);
        }

        return $this;
    }

    public function removeDiscogsVideo(DiscogsVideo $discogsVideo): self
    {
        if ($this->discogsVideos->removeElement($discogsVideo)) {
            $discogsVideo->removeArtist($this);
        }

        return $this;
    }

}
