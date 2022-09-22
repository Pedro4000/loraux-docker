<?php

namespace App\Entity;

use App\Repository\DiscogsClassRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscogsClassRepository::class)]
class DiscogsClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $discogsId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $fullyScrapped = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

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
}
