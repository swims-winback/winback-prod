<?php

namespace App\Entity\Main;

use App\Repository\StatisticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatisticRepository::class)]
class Statistic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $SN = null;

    #[ORM\Column(length: 255)]
    private ?string $version = null;

    #[ORM\Column(length: 255)]
    private ?string $zone = null;

    #[ORM\Column(length: 255)]
    private ?string $type_patho = null;

    #[ORM\Column(length: 255)]
    private ?string $patho = null;

    #[ORM\Column]
    private ?int $num_seance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accessoires = null;

    #[ORM\Column(nullable: true)]
    private ?int $douleur_av = null;

    #[ORM\Column(nullable: true)]
    private ?int $douleur_ap = null;

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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(string $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getTypePatho(): ?string
    {
        return $this->type_patho;
    }

    public function setTypePatho(string $type_patho): self
    {
        $this->type_patho = $type_patho;

        return $this;
    }

    public function getPatho(): ?string
    {
        return $this->patho;
    }

    public function setPatho(string $patho): self
    {
        $this->patho = $patho;

        return $this;
    }

    public function getNumSeance(): ?int
    {
        return $this->num_seance;
    }

    public function setNumSeance(int $num_seance): self
    {
        $this->num_seance = $num_seance;

        return $this;
    }

    public function getAccessoires(): ?string
    {
        return $this->accessoires;
    }

    public function setAccessoires(?string $accessoires): self
    {
        $this->accessoires = $accessoires;

        return $this;
    }

    public function getDouleurAv(): ?int
    {
        return $this->douleur_av;
    }

    public function setDouleurAv(?int $douleur_av): self
    {
        $this->douleur_av = $douleur_av;

        return $this;
    }

    public function getDouleurAp(): ?int
    {
        return $this->douleur_ap;
    }

    public function setDouleurAp(?int $douleur_ap): self
    {
        $this->douleur_ap = $douleur_ap;

        return $this;
    }
}
