<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Text
 *
 * @ORM\Table(name="texts")
 * @ORM\Entity
 */
class Text
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
     * @ORM\Column(name="Key", type="text", length=65535, nullable=false)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="French", type="text", length=65535, nullable=false)
     */
    private $french;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getFrench(): ?string
    {
        return $this->french;
    }

    public function setFrench(string $french): self
    {
        $this->french = $french;

        return $this;
    }


}
