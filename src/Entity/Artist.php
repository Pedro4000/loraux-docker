<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
#[ORM\Table(name: 'artist')]
class Artist
{

    public function __construct()
    {
        $this->releases = new ArrayCollection();
        $this->tracks = new ArrayCollection();
        $this->saluts = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column]
    #[Assert\Type('int')]
    private ?int $discogsId;

    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    private $name;

    #[ORM\ManyToMany(targetEntity:Release::class, mappedBy:"artists")]
    private Collection $releases;

    #[ORM\ManyToMany(targetEntity:Track::class, mappedBy:"artists")]
    private Collection $tracks;

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
    public function getReleases()
    {
        return $this->releases;
    }

    /**
     * @param mixed $releases
     */
    public function setReleases($releases): void
    {
        $this->releases = $releases;
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

}
