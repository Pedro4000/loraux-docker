<?php

namespace App\Entity;

use App\Repository\LabelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LabelRepository::class)
 * @ORM\Table(name="`label`")
 */
class Label
{


    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->releases = new ArrayCollection();
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Release", mappedBy="labels")
     */
    private $releases;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastTimeFullyScraped;

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
