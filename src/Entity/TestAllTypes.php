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

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $nullableDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nullableString = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'testAllTypes')]
    private Collection $ManyToMany2;

    #[ORM\ManyToMany(targetEntity: Track::class)]
    private Collection $ManyToManyNullablke;

    #[ORM\Column(length: 255)]
    private ?string $exit = null;


    public function __construct()
    {
        $this->manytomanyExemple = new ArrayCollection();
        $this->ManyToMany2 = new ArrayCollection();
        $this->ManyToManyNullablke = new ArrayCollection();
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

    public function getNullableDate(): ?\DateTimeInterface
    {
        return $this->nullableDate;
    }

    public function setNullableDate(?\DateTimeInterface $nullableDate): self
    {
        $this->nullableDate = $nullableDate;

        return $this;
    }

    public function getNullableString(): ?string
    {
        return $this->nullableString;
    }

    public function setNullableString(?string $nullableString): self
    {
        $this->nullableString = $nullableString;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getManyToMany2(): Collection
    {
        return $this->ManyToMany2;
    }

    public function addManyToMany2(User $manyToMany2): self
    {
        if (!$this->ManyToMany2->contains($manyToMany2)) {
            $this->ManyToMany2->add($manyToMany2);
        }

        return $this;
    }

    public function removeManyToMany2(User $manyToMany2): self
    {
        $this->ManyToMany2->removeElement($manyToMany2);

        return $this;
    }

    /**
     * @return Collection<int, Track>
     */
    public function getManyToManyNullablke(): Collection
    {
        return $this->ManyToManyNullablke;
    }

    public function addManyToManyNullablke(Track $manyToManyNullablke): self
    {
        if (!$this->ManyToManyNullablke->contains($manyToManyNullablke)) {
            $this->ManyToManyNullablke->add($manyToManyNullablke);
        }

        return $this;
    }

    public function removeManyToManyNullablke(Track $manyToManyNullablke): self
    {
        $this->ManyToManyNullablke->removeElement($manyToManyNullablke);

        return $this;
    }

    public function getExit(): ?string
    {
        return $this->exit;
    }

    public function setExit(string $exit): self
    {
        $this->exit = $exit;

        return $this;
    }
}
