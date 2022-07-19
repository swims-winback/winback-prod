<?php

namespace App\Entity;

use App\Repository\SoftwareRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SoftwareRepository::class)]

class Software
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $version;

    #[ORM\ManyToOne(targetEntity: DeviceFamily::class, inversedBy: 'software')]
    //#[ORM\Column(type: 'string', length: 255)]
    #[ORM\JoinColumn(nullable: true)]
    private $deviceFamily;

    #[ORM\Column(type: 'string', nullable: true)]
    private $softwareFile;


    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'created_at', type: "datetime_immutable", nullable: true)]
    private $created_at;
    

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(name: 'updated_at', type: "datetime", nullable: true)]
    private $updated_at;

    #[ORM\ManyToMany(targetEntity: Device::class, mappedBy: 'software')]
    private $devices;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->version;
    }
    
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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getFamily(): ?DeviceFamily
    {
        return $this->deviceFamily;
    }

    public function setFamily(?DeviceFamily $deviceFamily): self
    {
        $this->deviceFamily = $deviceFamily;

        return $this;
    }

    public function getSoftwareFile(): ?string
    {
        return $this->softwareFile;
    }

    public function setSoftwareFile(string $softwareFile): self
    {
        $this->softwareFile = $softwareFile;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

/*     public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    } */

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

 /*    public function setUpdatedAt(\DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    } */

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
            $device->addSoftware($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->removeElement($device)) {
            $device->removeSoftware($this);
        }

        return $this;
    }
}
