<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LabelRepository::class)]
#[ORM\Table(name: 'label')]
class Label extends DiscogsClass
{
    public function __construct()
    {
        $this->releases = new ArrayCollection();
        $this->discogsVideos = new ArrayCollection();
    }


    #[ORM\ManyToMany(targetEntity: Release::class, mappedBy:"labels")]
    private $releases;


    #[ORM\OneToMany(mappedBy: 'label', targetEntity: DiscogsVideo::class)]
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
            $discogsVideo->setLabel($this);
        }

        return $this;
    }

    public function removeDiscogsVideo(DiscogsVideo $discogsVideo): self
    {
        if ($this->discogsVideos->removeElement($discogsVideo)) {
            // set the owning side to null (unless already changed)
            if ($discogsVideo->getLabel() === $this) {
                $discogsVideo->setLabel(null);
            }
        }

        return $this;
    }

}
