<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Text
 *
 * @ORM\Table(name="prices")
 * @ORM\Entity
 */
class Price
{
    /**
     * @var int
     *
     * @ORM\Column(name="Id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Service", type="text", length=65535, nullable=false)
     */
    private $service;

    /**
     * @var float
     *
     * @ORM\Column(name="Price", type="float", nullable=false)
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="Sex", type="integer", nullable=false)
     */
    private $sex;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSex(): ?int
    {
        return $this->sex;
    }

    public function setSex(int $sex): self
    {
        $this->sex = $sex;

        return $this;
    }
}
