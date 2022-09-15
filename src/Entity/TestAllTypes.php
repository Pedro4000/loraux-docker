<?php

namespace App\Entity;

use App\Repository\TestAllTypesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestAllTypesRepository::class)]
class TestAllTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $stringExemple = null;
    
    #[ORM\Column(type: Types::ARRAY)]
    private array $arrayExemple = [];

    #[ORM\Column]
    private ?bool $boolExemple = null;

    #[ORM\Column]
    private ?int $callableExemple = null;

    #[ORM\Column]
    private ?float $floatExemple = null;

    #[ORM\ManyToOne]
    private ?Release $manytooneExemple = null;

    #[ORM\ManyToMany(targetEntity: Label::class)]
    private Collection $manytomanyExemple;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Track $onetooneExemple = null;

    #[ORM\Column(type: Types::OBJECT)]
    private ?object $objetExemple = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetimeExemple = null;


    public function __construct()
    {
        $this->manytomanyExemple = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArrayExemple(): array
    {
        return $this->arrayExemple;
    }

    public function setArrayExemple(array $arrayExemple): self
    {
        $this->arrayExemple = $arrayExemple;

        return $this;
    }

    public function isBoolExemple(): ?bool
    {
        return $this->boolExemple;
    }

    public function setBoolExemple(bool $boolExemple): self
    {
        $this->boolExemple = $boolExemple;

        return $this;
    }

    public function getCallableExemple(): ?int
    {
        return $this->callableExemple;
    }

    public function setCallableExemple(int $callableExemple): self
    {
        $this->callableExemple = $callableExemple;

        return $this;
    }

    public function getFloatExemple(): ?float
    {
        return $this->floatExemple;
    }

    public function setFloatExemple(float $floatExemple): self
    {
        $this->floatExemple = $floatExemple;

        return $this;
    }

    public function getManytooneExemple(): ?Release
    {
        return $this->manytooneExemple;
    }

    public function setManytooneExemple(?Release $manytooneExemple): self
    {
        $this->manytooneExemple = $manytooneExemple;

        return $this;
    }

    /**
     * @return Collection<int, Label>
     */
    public function getManytomanyExemple(): Collection
    {
        return $this->manytomanyExemple;
    }

    public function addManytomanyExemple(Label $manytomanyExemple): self
    {
        if (!$this->manytomanyExemple->contains($manytomanyExemple)) {
            $this->manytomanyExemple->add($manytomanyExemple);
        }

        return $this;
    }

    public function removeManytomanyExemple(Label $manytomanyExemple): self
    {
        $this->manytomanyExemple->removeElement($manytomanyExemple);

        return $this;
    }

    public function getOnetooneExemple(): ?Track
    {
        return $this->onetooneExemple;
    }

    public function setOnetooneExemple(Track $onetooneExemple): self
    {
        $this->onetooneExemple = $onetooneExemple;

        return $this;
    }

    public function getObjetExemple(): ?object
    {
        return $this->objetExemple;
    }

    public function setObjetExemple(object $objetExemple): self
    {
        $this->objetExemple = $objetExemple;

        return $this;
    }

    public function getDatetimeExemple(): ?\DateTimeInterface
    {
        return $this->datetimeExemple;
    }

    public function setDatetimeExemple(\DateTimeInterface $datetimeExemple): self
    {
        $this->datetimeExemple = $datetimeExemple;

        return $this;
    }

    public function getStringExemple(): ?string
    {
        return $this->stringExemple;
    }

    public function setStringExemple(string $stringExemple): self
    {
        $this->stringExemple = $stringExemple;

        return $this;
    }
}
