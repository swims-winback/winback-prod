<?php

namespace App\Entity\Main;

use App\Repository\ErrorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ErrorRepository::class)]
class Error
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\ManyToOne(targetEntity: ErrorFamily::class)]
    #[ORM\JoinColumn(nullable: false, referencedColumnName:"error_id")]
    //#[ORM\Column]
    private $error;
    

    #[ORM\ManyToOne(targetEntity: Device::class)]
    #[ORM\JoinColumn(nullable: false, referencedColumnName:"sn")]
    //#[ORM\Column(length: 255)]
    private $sn;

    #[ORM\Column(length: 255)]
    private ?string $version = null;

    //#[ORM\Column(type: Types::DATETIME_MUTABLE)]
    //#[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(length: 255)]
    //private ?\DateTimeInterface $date = null;
    private ?string $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function getError(): ?ErrorFamily
    {
        return $this->error;
    }

    public function setError(?ErrorFamily $error): self
    {
        $this->error = $error;

        return $this;
    }
    
    public function getSn(): ?Device
    {
        return $this->sn;
    }

    public function setSn(?Device $sn): self
    {
        $this->sn = $sn;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }
}
