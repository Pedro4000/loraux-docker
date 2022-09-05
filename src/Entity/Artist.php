<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArtistRepository::class)
 * @ORM\Table(name="`artist`")
 */
class Artist
{

    public function __construct()
    {
        $this->releases = new ArrayCollection();
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Release", mappedBy="artists")
     */
    private $releases;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Track", mappedBy="artists")
     */
    private $tracks;

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
