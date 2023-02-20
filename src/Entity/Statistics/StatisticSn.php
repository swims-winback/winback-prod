<?php

namespace App\Entity\Statistics;

use App\Repository\Statistics\StatisticSnRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatisticSnRepository::class)]
class StatisticSn
{
    
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $SN = null;

    public function getSN(): ?string
    {
        return $this->SN;
    }

    public function setSN(string $SN): self
    {
        $this->SN = $SN;

        return $this;
    }
}
