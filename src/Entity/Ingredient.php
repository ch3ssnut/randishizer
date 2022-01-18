<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 */
class Ingredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=35)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Dish::class, inversedBy="ingredients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dish;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ammount;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $unit;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    public function setDish(?Dish $dish): self
    {
        $this->dish = $dish;

        return $this;
    }

    public function getAmmount(): ?float
    {
        return $this->ammount;
    }

    public function setAmmount(?float $ammount): self
    {
        $this->ammount = $ammount;

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

    public function __toString()
    {
        return $this->name;
        
    }
}
