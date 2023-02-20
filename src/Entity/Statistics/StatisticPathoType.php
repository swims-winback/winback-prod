<?php

namespace App\Entity\Statistics;

use App\Repository\Statistics\StatisticPathoTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatisticPathoTypeRepository::class)]
class StatisticPathoType
{

    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $type_patho = null;

    public function getTypePatho(): ?string
    {
        return $this->type_patho;
    }

    public function setTypePatho(string $type_patho): self
    {
        $this->type_patho = $type_patho;

        return $this;
    }
}
