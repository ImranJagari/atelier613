<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="teams")
 * @ORM\Entity
 */
class Team
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
     * @ORM\Column(name="Firstname", type="text", length=65535, nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="Lastname", type="text", length=65535, nullable=false)
     */
    private $lastname;
    /**
     * @var string
     *
     * @ORM\Column(name="ImgPath", type="text", length=65535, nullable=false)
     */
    private $imgpath;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getImgpath(): ?string
    {
        return $this->imgpath;
    }

    public function setImgpath(string $imgpath): self
    {
        $this->imgpath = $imgpath;

        return $this;
    }

}
