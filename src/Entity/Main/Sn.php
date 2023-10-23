<?php

namespace App\Entity\Main;

use App\Repository\SnRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SnRepository::class)]
class Sn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $SN = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Device = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    //#[ORM\Column(name: 'Date', type: "datetime", nullable: true)]
    private ?\DateTimeInterface $Date = null;

    /*
    #[ORM\ManyToOne(inversedBy: 'serial_number')]
    private ?Client $client = null;
    */
    //private ?string $Date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSN(): ?string
    {
        return $this->SN;
    }

    public function setSN(string $SN): self
    {
        $this->SN = $SN;

        return $this;
    }
    
    public function getDevice(): ?string
    {
        return $this->Device;
    }

    public function setDevice(?string $Device): self
    {
        $this->Device = $Device;

        return $this;
    }
    
    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(?\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    /*
    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
    */
}
