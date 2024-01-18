<?php

namespace App\Entity\Main;

use App\Repository\ProtocolRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProtocolRepository::class)]
class Protocol
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sn = null;

    #[ORM\Column]
    private ?int $protocol_id = null;

    #[ORM\Column]
    private ?int $step_id = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;

    #[ORM\Column(length: 255)]
    private ?string $full_date = null;

    #[ORM\Column(length: 255)]
    private ?string $way_id = null;

    #[ORM\Column(length: 255)]
    private ?string $mode_id = null;

    #[ORM\Column]
    private ?int $param1 = null;

    #[ORM\Column]
    private ?int $param2 = null;

    #[ORM\Column]
    private ?int $param3 = null;

    #[ORM\Column]
    private ?int $time_tot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): static
    {
        $this->sn = $sn;

        return $this;
    }

    public function getProtocolId(): ?int
    {
        return $this->protocol_id;
    }

    public function setProtocolId(int $protocol_id): static
    {
        $this->protocol_id = $protocol_id;

        return $this;
    }

    public function getStepId(): ?int
    {
        return $this->step_id;
    }

    public function setStepId(int $step_id): static
    {
        $this->step_id = $step_id;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getFullDate(): ?string
    {
        return $this->full_date;
    }

    public function setFullDate(string $full_date): static
    {
        $this->full_date = $full_date;

        return $this;
    }

    public function getWayId(): ?int
    {
        return $this->way_id;
    }

    public function setWayId(int $way_id): static
    {
        $this->way_id = $way_id;

        return $this;
    }

    public function getModeId(): ?int
    {
        return $this->mode_id;
    }

    public function setModeId(int $mode_id): static
    {
        $this->mode_id = $mode_id;

        return $this;
    }

    public function getParam1(): ?int
    {
        return $this->param1;
    }

    public function setParam1(int $param1): static
    {
        $this->param1 = $param1;

        return $this;
    }

    public function getParam2(): ?int
    {
        return $this->param2;
    }

    public function setParam2(int $param2): static
    {
        $this->param2 = $param2;

        return $this;
    }

    public function getParam3(): ?int
    {
        return $this->param3;
    }

    public function setParam3(int $param3): static
    {
        $this->param3 = $param3;

        return $this;
    }

    public function getTimeTot(): ?int
    {
        return $this->time_tot;
    }

    public function setTimeTot(int $time_tot): static
    {
        $this->time_tot = $time_tot;

        return $this;
    }
}
