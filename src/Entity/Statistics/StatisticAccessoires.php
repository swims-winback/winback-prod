<?php

namespace App\Entity\Statistics;

use App\Repository\Statistics\StatisticAccessoiresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatisticAccessoiresRepository::class)]
class StatisticAccessoires
{
    #[ORM\Id]

    #[ORM\Column(length: 255)]
    private ?string $accessoires = null;

    public function getAccessoires(): ?string
    {
        return $this->accessoires;
    }

    public function setAccessoires(string $accessoires): self
    {
        $this->accessoires = $accessoires;

        return $this;
    }
}
