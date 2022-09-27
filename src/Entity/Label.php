<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use App\Entity\DiscogsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\DiscogsClass;

#[ORM\Entity(repositoryClass: LabelRepository::class)]
#[ORM\Table(name: 'label')]
class Label extends DiscogsClass
{
    public function __construct()
    {
        $this->releases = new ArrayCollection();
        $this->discogsVideos = new ArrayCollection();
    }
   

    #[ORM\ManyToMany(targetEntity: Release::class, mappedBy:"labels", cascade:["persist"])]
    private Collection $releases;


    #[ORM\OneToMany(targetEntity: DiscogsVideo::class, mappedBy: 'label')]
    private Collection $discogsVideos;

    #[ORM\OneToOne(targetEntity: PendingYoutubeTask::class, inversedBy: 'label')]
    private $pendingYoutubeTask;

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

    /**
	 * @return mixed
	 */
	function getPendingYoutubeTask() {
		return $this->pendingYoutubeTask;
	}
	
	/**
	 * @param mixed $pendingYoutubeTask 
	 * @return Label
	 */
	function setPendingYoutubeTask($pendingYoutubeTask): self {
		$this->pendingYoutubeTask = $pendingYoutubeTask;
		return $this;
	}

}
