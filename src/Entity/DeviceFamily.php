<?php

namespace App\Entity;

use App\Repository\DeviceFamilyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeviceFamilyRepository::class)]
class DeviceFamily
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'deviceFamily', targetEntity: Software::class)]
    private $software;

    #[ORM\Column(type: 'string', nullable: true, length: 255)]
    private $therapyType;

    #[ORM\OneToMany(mappedBy: 'deviceFamily', targetEntity: Device::class)]
    private $devices;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    private $hexaId;

    #[ORM\Column(type: 'integer', nullable: true, unique: true)]
    private $numberId;

    public function __construct()
    {
        $this->software = new ArrayCollection();
        $this->devices = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name;
    }
    /*
    public function __toString()
    {
        return $this->numberId;
    }
    */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Software>
     */

    public function getSoftware(): Collection
    {
        return $this->software;
    }

    public function addSoftware(Software $software): self
    {
        if (!$this->software->contains($software)) {
            $this->software[] = $software;
            $software->setFamily($this);
        }

        return $this;
    }

    public function removeSoftware(Software $software): self
    {
        if ($this->software->removeElement($software)) {
            // set the owning side to null (unless already changed)
            if ($software->getFamily() === $this) {
                $software->setFamily(null);
            }
        }

        return $this;
    }

    public function getTherapyType(): ?string
    {
        return $this->therapyType;
    }

    public function setTherapyType(string $therapyType): self
    {
        $this->therapyType = $therapyType;

        return $this;
    }

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setDeviceFamily($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getDeviceFamily() === $this) {
                $device->setDeviceFamily(null);
            }
        }

        return $this;
    }

    public function getHexaId(): ?string
    {
        return $this->hexaId;
    }

    public function setHexaId(string $hexaId): self
    {
        $this->hexaId = $hexaId;

        return $this;
    }

    public function getNumberId(): ?string
    {
        return $this->numberId;
    }

    public function setNumberId(string $numberId): self
    {
        $this->numberId = $numberId;

        return $this;
    }

}
