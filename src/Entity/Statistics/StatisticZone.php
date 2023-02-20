<?php

namespace App\Entity\Statistics;

use App\Repository\Statistics\StatisticZoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatisticZoneRepository::class)]
class StatisticZone
{
    #[ORM\Id]

    #[ORM\Column(length: 255)]
    private ?string $zone = null;

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(string $zone): self
    {
        $this->zone = $zone;

        return $this;
    }
}
