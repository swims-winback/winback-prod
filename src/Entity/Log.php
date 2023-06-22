<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    //#[ORM\ManyToOne(inversedBy: 'logs')]
    //#[ORM\JoinColumn(nullable: false)]
    //private ?Device $serial_number = null;
    private ?string $serial_number = null;

    //#[ORM\Column(type: Types::DATE_MUTABLE)]
    //#[ORM\Column(name: 'date', type: "datetime_immutable", nullable: true)]
    #[ORM\Column(length: 255)]
    //private ?\DateTimeInterface $date = null;
    private ?string $date = null;
    
    #[ORM\Column(length: 255)]
    private ?string $event = null;

    #[ORM\Column(length: 255)]
    private ?string $bloc_id = null;

    #[ORM\Column(length: 255)]
    private ?string $step_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way1_acc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way2_acc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mode1_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mode1_intensite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mode1_param2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mode1_param3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mode2_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mode2_intensite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mode2_param2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mode2_param3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way1_contact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way1_Iin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way1_Iout = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way1_Vout = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way2_contact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way2_Iin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way2_Iout = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $way2_Vout = null;

    #[ORM\Column(length: 255)]
    private ?string $time_tot = null;

    #[ORM\Column(length: 255)]
    private ?string $steps_id = null;

    #[ORM\Column(length: 255)]
    private ?string $time_contact = null;

    #[ORM\Column(length: 255)]
    private ?string $time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    //public function getSerialNumber(): ?Device
    public function getSerialNumber(): ?string
    {
        return $this->serial_number;
    }

    //public function setSerialNumber(?Device $serial_number): self
    public function setSerialNumber(string $serial_number): self
    {
        $this->serial_number = $serial_number;

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

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getBlocId(): ?string
    {
        return $this->bloc_id;
    }

    public function setBlocId(string $bloc_id): self
    {
        $this->bloc_id = $bloc_id;

        return $this;
    }

    public function getStepId(): ?string
    {
        return $this->step_id;
    }

    public function setStepId(string $step_id): self
    {
        $this->step_id = $step_id;

        return $this;
    }

    public function getWay1Acc(): ?string
    {
        return $this->way1_acc;
    }

    public function setWay1Acc(?string $way1_acc): self
    {
        $this->way1_acc = $way1_acc;

        return $this;
    }

    public function getWay2Acc(): ?string
    {
        return $this->way2_acc;
    }

    public function setWay2Acc(?string $way2_acc): self
    {
        $this->way2_acc = $way2_acc;

        return $this;
    }

    public function getMode1Id(): ?string
    {
        return $this->mode1_id;
    }

    public function setMode1ModeId(?string $mode1_id): self
    {
        $this->mode1_id = $mode1_id;

        return $this;
    }

    public function getMode1Intensite(): ?string
    {
        return $this->mode1_intensite;
    }

    public function setMode1Intensite(?string $mode1_intensite): self
    {
        $this->mode1_intensite = $mode1_intensite;

        return $this;
    }

    public function getMode1Param2(): ?string
    {
        return $this->mode1_param2;
    }

    public function setMode1Param2(?string $mode1_param2): self
    {
        $this->mode1_param2 = $mode1_param2;

        return $this;
    }

    public function getMode1Param3(): ?string
    {
        return $this->mode1_param3;
    }

    public function setMode1Param3(?string $mode1_param3): self
    {
        $this->mode1_param3 = $mode1_param3;

        return $this;
    }

    public function getMode2Id(): ?string
    {
        return $this->mode2_id;
    }

    public function setMode2Id(?string $mode2_id): self
    {
        $this->mode2_id = $mode2_id;

        return $this;
    }

    public function getMode2Intensite(): ?string
    {
        return $this->mode2_intensite;
    }

    public function setMode2Intensite(?string $mode2_intensite): self
    {
        $this->mode2_intensite = $mode2_intensite;

        return $this;
    }

    public function getMode2Param2(): ?string
    {
        return $this->mode2_param2;
    }

    public function setMode2Param2(?string $mode2_param2): self
    {
        $this->mode2_param2 = $mode2_param2;

        return $this;
    }

    public function getMode2Param3(): ?string
    {
        return $this->mode2_param3;
    }

    public function setMode2Param3(?string $mode2_param3): self
    {
        $this->mode2_param3 = $mode2_param3;

        return $this;
    }

    public function getWay1Contact(): ?string
    {
        return $this->way1_contact;
    }

    public function setWay1Contact(?string $way1_contact): self
    {
        $this->way1_contact = $way1_contact;

        return $this;
    }

    public function getWay1Iin(): ?string
    {
        return $this->way1_Iin;
    }

    public function setWay1Iin(?string $way1_Iin): self
    {
        $this->way1_Iin = $way1_Iin;

        return $this;
    }

    public function getWay1Iout(): ?string
    {
        return $this->way1_Iout;
    }

    public function setWay1Iout(?string $way1_Iout): self
    {
        $this->way1_Iout = $way1_Iout;

        return $this;
    }

    public function getWay1Vout(): ?string
    {
        return $this->way1_Vout;
    }

    public function setWay1Vout(?string $way1_Vout): self
    {
        $this->way1_Vout = $way1_Vout;

        return $this;
    }

    public function getWay2Contact(): ?string
    {
        return $this->way2_contact;
    }

    public function setWay2Contact(?string $way2_contact): self
    {
        $this->way2_contact = $way2_contact;

        return $this;
    }

    public function getWay2Iin(): ?string
    {
        return $this->way2_Iin;
    }

    public function setWay2Iin(?string $way2_Iin): self
    {
        $this->way2_Iin = $way2_Iin;

        return $this;
    }

    public function getWay2Iout(): ?string
    {
        return $this->way2_Iout;
    }

    public function setWay2Iout(?string $way2_Iout): self
    {
        $this->way2_Iout = $way2_Iout;

        return $this;
    }

    public function getWay2Vout(): ?string
    {
        return $this->way2_Vout;
    }

    public function setWay2Vout(?string $way2_Vout): self
    {
        $this->way2_Vout = $way2_Vout;

        return $this;
    }

    public function getTimeTot(): ?string
    {
        return $this->time_tot;
    }

    public function setTimeTot(string $time_tot): self
    {
        $this->time_tot = $time_tot;

        return $this;
    }

    public function getStepsId(): ?string
    {
        return $this->steps_id;
    }

    public function setStepsId(string $steps_id): self
    {
        $this->steps_id = $steps_id;

        return $this;
    }

    public function getTimeContact(): ?string
    {
        return $this->time_contact;
    }

    public function setTimeContact(string $time_contact): self
    {
        $this->time_contact = $time_contact;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }
}
