<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column(length: 255)]
    private string $firstName;

    #[ORM\Column(length: 255)]

    private string $familyName;

    #[ORM\Column(length: 255)]

    private string $emailAddress;

    #[ORM\Column(length: 255)]

    private string $password;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $birthDate;

    #[ORM\Column(length: 255)]
    private string $sex;

    #[ORM\Column]
    private bool $isMailAddressVerified = false;

    #[ORM\ManyToMany(targetEntity: TestAllTypes::class, mappedBy: 'ManyToMany2')]
    private Collection $testAllTypes;

    public function __construct()
    {
        $this->testAllTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): self
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsMailAddressVerified()
    {
        return $this->isMailAddressVerified;
    }

    /**
     * @param mixed $isMailAddressVerified
     */
    public function setIsMailAddressVerified($isMailAddressVerified): void
    {
        $this->isMailAddressVerified = $isMailAddressVerified;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, TestAllTypes>
     */
    public function getTestAllTypes(): Collection
    {
        return $this->testAllTypes;
    }

    public function addTestAllType(TestAllTypes $testAllType): self
    {
        if (!$this->testAllTypes->contains($testAllType)) {
            $this->testAllTypes->add($testAllType);
            $testAllType->addManyToMany2($this);
        }

        return $this;
    }

    public function removeTestAllType(TestAllTypes $testAllType): self
    {
        if ($this->testAllTypes->removeElement($testAllType)) {
            $testAllType->removeManyToMany2($this);
        }

        return $this;
    }
}

