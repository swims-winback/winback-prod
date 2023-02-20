<?php

namespace App\Entity\Statistics;

use App\Repository\Statistics\StatisticPathoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatisticPathoRepository::class)]
class StatisticPatho
{
    #[ORM\Id]

    #[ORM\Column(length: 255)]
    private ?string $patho = null;

    public function getPatho(): ?string
    {
        return $this->patho;
    }

    public function setPatho(string $patho): self
    {
        $this->patho = $patho;

        return $this;
    }
}
