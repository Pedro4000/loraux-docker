<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LabelRepository::class)]
#[ORM\Table(name: 'label')]
class Label
{
    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->releases = new ArrayCollection();
    }


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column]
    private ?int $discogsid;

    #[ORM\ManyToMany(targetEntity: Release::class, mappedBy:"labels")]
    private $releases;

    #[Column(length:255)]
    #[asset(length:255)]
    private string $name;

    #[ORM\OneToOne(targetEntity: PendingYoutubeTask::class, inversedBy: 'label')]
    private $pendingYoutubeTask;

    #[Column(nullable:true)]
    private string $lastTimeFullyScraped;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDiscogsId()
    {
        return $this->discogsId;
    }

    /**
     * @param mixed $discogsId
     */
    public function setDiscogsId($discogsId): void
    {
        $this->discogsId = $discogsId;
    }

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

    /**
     * @return mixed
     */
    public function getLastTimeFullyScraped()
    {
        return $this->lastTimeFullyScraped;
    }

    /**
     * @param mixed $lastTimeFullyScraped
     */
    public function setLastTimeFullyScraped($lastTimeFullyScraped): void
    {
        $this->lastTimeFullyScraped = $lastTimeFullyScraped;
    }



}
