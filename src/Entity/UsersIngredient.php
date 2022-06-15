<?php

namespace App\Entity;

use App\Repository\UsersIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsersIngredientRepository::class)
 */
class UsersIngredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $Ingredient;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="usersIngredients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIngredient(): ?string
    {
        return $this->Ingredient;
    }

    public function setIngredient(string $Ingredient): self
    {
        $this->Ingredient = $Ingredient;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->Owner;
    }

    public function setOwner(?User $Owner): self
    {
        $this->Owner = $Owner;

        return $this;
    }

    public function __toString()
    {
        return $this->Ingredient;
        
    }

}
