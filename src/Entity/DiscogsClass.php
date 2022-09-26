<?php

namespace App\Entity;

use App\Repository\ReleaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

abstract class DiscogsClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"AUTO")]
    #[ORM\Column]
    protected ?int $id = null;
    
    #[ORM\Column(unique:true)]
    protected ?int $discogsId = null;

    #[ORM\Column(length: 255)]
    protected ?string $name = null;

    #[ORM\Column]
    protected ?bool $fullyScrapped = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeImmutable $fullyScrappedDate = null;

    #[ORM\Column]
    protected ?\DateTimeImmutable $createdAt = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isFullyScrapped(): ?bool
    {
        return $this->fullyScrapped;
    }

    public function setFullyScrapped(bool $fullyScrapped): self
    {
        $this->fullyScrapped = $fullyScrapped;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

	/**
	 * @return \DateTimeImmutable
	 */
	function getFullyScrappedDate(): \DateTimeImmutable 
    {
		return $this->fullyScrappedDate;
	}
	
	/**
	 * @param \DateTimeImmutable $fullyScrappedDate 
	 * @return DiscogsClass
	 */
	function setFullyScrappedDate(\DateTimeImmutable $fullyScrappedDate): self 
    {
		$this->fullyScrappedDate = $fullyScrappedDate;
		return $this;
	}

}
