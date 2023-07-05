<?php

namespace App\Entity;

use App\Repository\ErrorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ErrorRepository::class)]
class Error
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $error_id = null;

    #[ORM\Column(length: 255)]
    private ?string $sn = null;

    #[ORM\Column(length: 255)]
    private ?string $version = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getErrorId(): ?int
    {
        return $this->error_id;
    }

    public function setErrorId(int $error_id): self
    {
        $this->error_id = $error_id;

        return $this;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): self
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
